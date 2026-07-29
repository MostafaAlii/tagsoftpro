<?php
namespace App\Repositories\Eloquents;
use App\DataTables\Dashboard\Admin\MenuItemDataTable;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Models\{MenuItem,Company, MenuNode};
use App\Http\Requests\Dashboard\MenuItem\StoreMenuItemRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request,JsonResponse};
use App\Actions\MenuItem\Bulk\BulkActionHandler;
use App\Services\Theme\ThemeIconResolver;
use App\Enums\MenuItem\MenuItemStatus;
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

    public function list(Request $request) {
        $locale = app()->getLocale();
        $perPage = 10;
        $page = max((int) $request->get('page', 1), 1);
        $search = trim((string) $request->get('search', ''));
        $trashed = $request->get('trashed', 'false') === 'true';
        $menuId = $request->get('menu_id');
        $query = MenuItem::query()->with(['translations' => fn($q) => $q->where('locale', $locale)]);
        if ($trashed) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
            if ($menuId) {
                $usedIds = MenuNode::where('menu_id', $menuId)->pluck('menu_item_id')->toArray();
                if (!empty($usedIds)) {
                    $query->whereNotIn('id', $usedIds);
                }
            }
        }
        if ($search !== '') {
            $query->whereHas('translations', function ($q) use ($search, $locale) {
                $q->where('locale', $locale)->where('title', 'like', "%{$search}%");
            });
        }
        $total = (clone $query)->count();
        $items = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function (MenuItem $item) {
                return [
                    'id' => $item->id,
                    'title' => $item->getTranslatedTitle(),
                    'icon' => $item->icon,
                    'type' => $item->type->value ?? $item->type,
                    'status' => $item->status->value ?? $item->status,
                    'is_owner_only' => (bool) $item->is_owner_only,
                ];
            });
        return response()->json([
            'success' => true,
            'items' => $items,
            'has_more' => ($page * $perPage) < $total,
            'next_page' => $page + 1,
            'total' => $total,
        ]);
    }

    /*public function store(StoreMenuItemRequest $request) {
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
    }*/
    public function store(StoreMenuItemRequest $request)
    {
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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/menu_items.created_successfully'),
                    'item' => [
                        'id' => $menuItem->id,
                        'title' => $menuItem->getTranslatedTitle(),
                        'icon' => $menuItem->icon,
                        'type' => $menuItem->type->value ?? $menuItem->type,
                        'status' => $menuItem->status->value ?? $menuItem->status,
                        'is_owner_only' => (bool) $menuItem->is_owner_only,
                    ],
                ]);
            }

            return redirect()->route('admin.menu_items.index')->with('success', trans('dashboard/menu_items.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
                ], 500);
            }

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

    public function toggleStatus(MenuItem $menuItem)
    {
        try {
            $newStatus = $menuItem->status === MenuItemStatus::ACTIVE
                ? MenuItemStatus::INACTIVE
                : MenuItemStatus::ACTIVE;

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

    public function toggleOwnerOnly(MenuItem $menuItem) {
        try {
            $menuItem->update(['is_owner_only' => !$menuItem->is_owner_only]);
            return response()->json([
                'success' => true,
                'is_owner_only' => (bool) $menuItem->is_owner_only,
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

    /*public function bulkAction(Request $request): JsonResponse {
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
    }*/
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $ids = $request->ids;
            $action = $request->action;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/menu_items.bulk_select_at_least_one'),
                ]);
            }

            switch ($action) {
                case 'delete':
                    MenuItem::whereIn('id', $ids)->get()->each->delete();
                    $message = trans('dashboard/menu_items.deleted_successfully');
                    break;

                case 'restore':
                    MenuItem::onlyTrashed()->whereIn('id', $ids)->get()->each->restore();
                    $message = trans('dashboard/menu_items.restored_successfully');
                    break;

                case 'force_delete':
                    MenuItem::onlyTrashed()->whereIn('id', $ids)->get()->each->forceDelete();
                    $message = trans('dashboard/menu_items.permanently_deleted');
                    break;

                case 'change_status':
                    $status = $request->input('status');
                    if (!in_array($status, \App\Enums\MenuItem\MenuItemStatus::values())) {
                        throw new \InvalidArgumentException(trans('dashboard/general.error_occurred'));
                    }
                    MenuItem::whereIn('id', $ids)->update(['status' => $status]);
                    $message = trans('dashboard/menu_items.status_updated');
                    break;

                default:
                    throw new \InvalidArgumentException(trans('dashboard/general.error_occurred'));
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: trans('dashboard/general.error_occurred'),
            ]);
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
