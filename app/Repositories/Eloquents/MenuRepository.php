<?php
namespace App\Repositories\Eloquents;
use App\DataTables\Dashboard\Admin\MenuDataTable;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Models\{Menu,Company};
use App\Http\Requests\Dashboard\Menu\StoreMenuRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request,JsonResponse};
use App\Actions\Menu\Bulk\BulkActionHandler;
use App\Services\Theme\ThemeIconResolver;

class MenuRepository implements MenuRepositoryInterface {
    public function index(MenuDataTable $menuDataTable) {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        $icons = ThemeIconResolver::all();
        $iconCssUrls = ThemeIconResolver::cssUrls();
        return $menuDataTable->render('dashboard.admin.menus.index', [
            'title' => trans('dashboard/menus.menus'),
            'companies' => $companies,
            'locales' => $locales,
            'icons' => $icons,
            'iconCssUrls' => $iconCssUrls,
        ]);
    }

    public function store(StoreMenuRequest $request) {
        try {
            DB::beginTransaction();
            $menu = Menu::create([
                'key' => $request->key,
                'icon' => $request->icon,
                'route_prefix' => $request->route_prefix,
                'status' => $request->status,
                'company_id' => $request->company_id,
            ]);

            if ($request->has('locales') && is_array($request->locales)) {
                foreach ($request->locales as $locale => $data) {
                    if (isset($data['name']) && !empty($data['name'])) {
                        $menu->translations()->create([
                            'locale' => $locale,
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.menus.index')
                ->with('success', trans('dashboard/menus.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.menus.index')
                ->with('error', trans('dashboard/general.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function edit(Menu $menu) {
        $menu->load('translations');
        return response()->json([
            'success' => true,
            'data' => $menu,
        ]);
    }

    public function update(Menu $menu, array $data, ?Request $request = null)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'key' => $data['key'] ?? $menu->key,
                'icon' => $data['icon'] ?? $menu->icon,
                'route_prefix' => $data['route_prefix'] ?? $menu->route_prefix,
                'status' => $data['status'] ?? $menu->status,
                'company_id' => $data['company_id'] ?? $menu->company_id,
            ];

            $menu->update($updateData);

            if (isset($data['locales']) && is_array($data['locales'])) {
                foreach ($data['locales'] as $locale => $translationData) {
                    if (isset($translationData['name']) && !empty($translationData['name'])) {
                        $menu->translations()->updateOrCreate(
                            ['locale' => $locale],
                            [
                                'name' => $translationData['name'],
                                'description' => $translationData['description'] ?? null,
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/menus.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
    public function toggleStatus(Menu $menu) {
        try {
            $newStatus = $menu->status === 'active' ? 'inactive' : 'active';
            $menu->updateQuietly(['status' => $newStatus]);
            $menu->refresh();
            return response()->json([
                'success' => true,
                'badge' => $menu->status->badge(),
                'message' => trans('dashboard/menus.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Menu $menu) {
        try {
            $menu->delete();
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/menus.deleted_successfully'),
                ]);
            }
            return redirect()->route('admin.menus.index')->with('success', trans('dashboard/menus.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }
            return redirect()->route('admin.menus.index')->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function restore($id)
    {
        try {
            $menu = Menu::withTrashed()->findOrFail($id);
            $menu->restore();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/menus.restored_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $menu = Menu::withTrashed()->findOrFail($id);
            $menu->forceDelete();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/menus.permanently_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $ids = $request->ids;
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/menus.bulk_select_at_least_one'),
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

    public function hasTrashed()
    {
        return response()->json([
            'hasTrashed' => Menu::onlyTrashed()->exists()
        ]);
    }
}