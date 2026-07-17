<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\PermissionGroupDataTable;
use App\Repositories\Contracts\PermissionGroupRepositoryInterface;
use App\Models\{PermissionGroup,Company};
use App\Http\Requests\Dashboard\Permission\PermissionGroup\StorePermissionGroupRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request, JsonResponse};
use App\Actions\Permission\PermissionGroup\Bulk\BulkActionHandler;
class PermissionGroupRepository implements PermissionGroupRepositoryInterface {
    public function index(PermissionGroupDataTable $permissionGroupDataTable) {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        return $permissionGroupDataTable->render('dashboard.admin.permission.permission_groups.index', [
            'title' => trans('dashboard/permission_groups.permission_groups'),
            'companies' => $companies,
            'locales' => $locales,
        ]);
    }

    public function store(StorePermissionGroupRequest $request)
    {
        try {
            DB::beginTransaction();

            // ─── إنشاء المجموعة ──────────────────────────────────
            $permissionGroup = PermissionGroup::create([
                'icon' => $request->icon,
                'status' => $request->status,
                'company_id' => $request->company_id,
            ]);

            // ─── التأكد من وجود الترجمات ──────────────────────────
            if ($request->has('locales') && is_array($request->locales)) {
                foreach ($request->locales as $locale => $data) {
                    // التأكد من وجود name
                    if (isset($data['name']) && !empty($data['name'])) {
                        $permissionGroup->translations()->create([
                            'locale' => $locale,
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.permission_groups.index')
                ->with('success', trans('dashboard/permission_groups.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.permission_groups.index')
                ->with('error', trans('dashboard/general.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function update(PermissionGroup $permissionGroup, array $data, ?Request $request = null)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'icon' => $data['icon'] ?? $permissionGroup->icon,
                'status' => $data['status'] ?? $permissionGroup->status,
                'company_id' => $data['company_id'] ?? $permissionGroup->company_id,
            ];

            $permissionGroup->update($updateData);

            // ─── تحديث الترجمات ──────────────────────────────────
            if (isset($data['locales']) && is_array($data['locales'])) {
                foreach ($data['locales'] as $locale => $translationData) {
                    if (isset($translationData['name']) && !empty($translationData['name'])) {
                        $permissionGroup->translations()->updateOrCreate(
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
                'message' => trans('dashboard/permission_groups.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(PermissionGroup $permissionGroup) {
        $permissionGroup->load('translations');
        return response()->json([
            'success' => true,
            'data' => $permissionGroup,
        ]);
    }

    public function toggleStatus(PermissionGroup $permissionGroup) {
        try {
            $newStatus = $permissionGroup->status === 'active' ? 'inactive' : 'active';
            $permissionGroup->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'badge' => $permissionGroup->status->badge(),
                'message' => trans('dashboard/permission_groups.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(PermissionGroup $permissionGroup) {
        try {
            $permissionGroup->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/permission_groups.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.permission_groups.index')
                ->with('success', trans('dashboard/permission_groups.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.permission_groups.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function restore($id) {
        try {
            $permissionGroup = PermissionGroup::withTrashed()->findOrFail($id);
            $permissionGroup->restore();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/permission_groups.restored_successfully'),
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
            $permissionGroup = PermissionGroup::withTrashed()->findOrFail($id);
            $permissionGroup->forceDelete();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/permission_groups.permanently_deleted'),
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
                    'message' => trans('dashboard/permission_groups.bulk_select_at_least_one'),
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
            'hasTrashed' => PermissionGroup::onlyTrashed()->exists()
        ]);
    }
}