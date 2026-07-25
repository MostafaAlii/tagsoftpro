<?php
namespace App\Repositories\Eloquents;
use App\DataTables\Dashboard\Admin\MenuItemDataTable;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Models\{MenuItem,Company};
use App\Http\Requests\Dashboard\MenuItem\StoreMenuItemRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request,JsonResponse};
use App\Actions\MenuItem\Bulk\BulkActionHandler;
use App\Services\Theme\ThemeIconResolver;
class MenuItemRepository implements MenuItemRepositoryInterface {
    public function index(MenuItemDataTable $menuItemDataTable) {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        $icons = ThemeIconResolver::all();
        $iconCssUrls = ThemeIconResolver::cssUrls();
        return $menuItemDataTable->render('dashboard.admin.menu_items.index', [
            'title' => trans('dashboard/menu_items.menu_items'),
            'companies' => $companies,
            'locales' => $locales,
            'icons' => $icons,
            'iconCssUrls' => $iconCssUrls,
        ]);
    }

    public function store(StoreMenuItemRequest $request) {
        try {
            DB::beginTransaction();
            $menuItem = MenuItem::create([
                'type' => $request->type,
                'icon' => $request->icon,
                'link_type' => $request->link_type,
                'route_name' => $request->route_name,
                'route_params' => $request->route_params,
                'url' => $request->url,
                'target' => $request->target ?? '_self',
                'is_owner_only' => $request->is_owner_only ?? false,
                'permission_name' => $request->permission_name,
                'badge_text' => $request->badge_text,
                'badge_color' => $request->badge_color,
                'status' => $request->status,
                'visible_from' => $request->visible_from,
                'visible_until' => $request->visible_until,
                'company_id' => $request->company_id,
            ]);

            if ($request->has('locales') && is_array($request->locales)) {
                foreach ($request->locales as $locale => $data) {
                    if (isset($data['title']) && !empty($data['title'])) {
                        $menuItem->translations()->create([
                            'locale' => $locale,
                            'title' => $data['title'],
                            'description' => $data['description'] ?? null,
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.menu_items.index')->with('success', trans('dashboard/menu_items.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.menu_items.index')->with('error', trans('dashboard/general.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function edit(MenuItem $menuItem) {
        $menuItem->load('translations');
        return response()->json([
            'success' => true,
            'data' => $menuItem,
        ]);
    }

    public function update(MenuItem $menuItem, array $data, ?Request $request = null) {
        try {
            DB::beginTransaction();
            $updateData = [
                'type' => $data['type'] ?? $menuItem->type,
                'icon' => $data['icon'] ?? $menuItem->icon,
                'link_type' => $data['link_type'] ?? $menuItem->link_type,
                'route_name' => $data['route_name'] ?? $menuItem->route_name,
                'route_params' => $data['route_params'] ?? $menuItem->route_params,
                'url' => $data['url'] ?? $menuItem->url,
                'target' => $data['target'] ?? $menuItem->target,
                'is_owner_only' => $data['is_owner_only'] ?? $menuItem->is_owner_only,
                'permission_name' => $data['permission_name'] ?? $menuItem->permission_name,
                'badge_text' => $data['badge_text'] ?? $menuItem->badge_text,
                'badge_color' => $data['badge_color'] ?? $menuItem->badge_color,
                'status' => $data['status'] ?? $menuItem->status,
                'visible_from' => $data['visible_from'] ?? $menuItem->visible_from,
                'visible_until' => $data['visible_until'] ?? $menuItem->visible_until,
                'company_id' => $data['company_id'] ?? $menuItem->company_id,
            ];
            $menuItem->update($updateData);
            if (isset($data['locales']) && is_array($data['locales'])) {
                foreach ($data['locales'] as $locale => $translationData) {
                    if (isset($translationData['title']) && !empty($translationData['title'])) {
                        $menuItem->translations()->updateOrCreate(
                            ['locale' => $locale],
                            [
                                'title' => $translationData['title'],
                                'description' => $translationData['description'] ?? null,
                            ]
                        );
                    }
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/menu_items.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(MenuItem $menuItem) {
        try {
            $newStatus = $menuItem->status === 'active' ? 'inactive' : 'active';
            $menuItem->update(['status' => $newStatus]);
            return response()->json([
                'success' => true,
                'badge' => $menuItem->status->badge(),
                'message' => trans('dashboard/menu_items.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(MenuItem $menuItem) {
        try {
            $menuItem->delete();
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/menu_items.deleted_successfully'),
                ]);
            }
            return redirect()->route('admin.menu_items.index')->with('success', trans('dashboard/menu_items.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }
            return redirect()->route('admin.menu_items.index')->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function restore($id) {
        try {
            $menuItem = MenuItem::withTrashed()->findOrFail($id);
            $menuItem->restore();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/menu_items.restored_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function forceDelete($id) {
        try {
            $menuItem = MenuItem::withTrashed()->findOrFail($id);
            $menuItem->forceDelete();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/menu_items.permanently_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function bulkAction(Request $request): JsonResponse {
        try {
            $ids = $request->ids;
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/menu_items.bulk_select_at_least_one'),
                ]);
            }
            $message = app(BulkActionHandler::class)->handle(
                action: $request->action,
                ids: $ids,
                params: $request->only(['status'])
            );
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function hasTrashed() {
        return response()->json([
            'hasTrashed' => MenuItem::onlyTrashed()->exists()
        ]);
    }
}