<?php
namespace Modules\Vendor\Repositories;
use Modules\Vendor\Repositories\VendorRepositoryInterface;
use Modules\Vendor\Entities\{Vendor};
use App\Models\{Company, Department};
use Modules\Vendor\Http\Requests\StoreVendorRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\{Request, JsonResponse};
use Modules\Vendor\DataTables\VendorDataTable;
class VendorRepository implements VendorRepositoryInterface {
    public function index(VendorDataTable $vendorDataTable)
    {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $departments = Department::whereStatus('active')->get();

        return $vendorDataTable->render('vendor::index', [
            'title' => trans('vendor::vendors.vendors'),
            'companies' => $companies,
            'departments' => $departments,
        ]);
    }

    public function store(StoreVendorRequest $request) {
        try {
            DB::beginTransaction();
            $vendor = Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'type' => $request->type,
                'password' => bcrypt($request->password),
                'date' => $request->date,
                'company_id' => $request->company_id,
                'department_id' => $request?->department_id,
                'created_by' => get_user_data()->id,
            ]);
            DB::commit();
            return redirect()->route('admin.vendors.index')->with('success', trans('vendor::vendors.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.vendors.index')->with('error', trans('dashboard/general.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function edit(Vendor $vendor)
    {
        return response()->json([
            'success' => true,
            'data' => $vendor,
        ]);
    }

    public function update(Vendor $vendor, array $data, ?Request $request = null)
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $data['name'] ?? $vendor->name,
                'email' => $data['email'] ?? $vendor->email,
                'phone' => $data['phone'] ?? $vendor->phone,
                'status' => $data['status'] ?? $vendor->status,
                'type' => $data['type'] ?? $vendor->type,
                'date' => $data['date'] ?? $vendor->date,
                'company_id' => $data['company_id'] ?? $vendor->company_id,
                'department_id' => $data['department_id'] ?? $vendor->department_id,
                'updated_by' => get_user_data()->id,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = bcrypt($data['password']);
            }

            $vendor->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('vendor::vendors.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(Vendor $vendor)
    {
        try {
            $statuses = ['active', 'inactive', 'pending', 'suspended'];
            $currentIndex = array_search($vendor->status, $statuses);
            $newIndex = ($currentIndex + 1) % count($statuses);
            $newStatus = $statuses[$newIndex];

            $vendor->updateQuietly(['status' => $newStatus]);
            $vendor->refresh();

            return response()->json([
                'success' => true,
                'badge' => $vendor->status_badge,
                'message' => trans('vendor::vendors.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('vendor::vendors.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.vendors.index')
                ->with('success', trans('vendor::vendors.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.vendors.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function restore($id)
    {
        try {
            $vendor = Vendor::withTrashed()->findOrFail($id);
            $vendor->restore();

            return response()->json([
                'success' => true,
                'message' => trans('vendor::vendors.restored_successfully'),
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
            $vendor = Vendor::withTrashed()->findOrFail($id);
            $vendor->forceDelete();

            return response()->json([
                'success' => true,
                'message' => trans('vendor::vendors.permanently_deleted'),
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
                    'message' => trans('vendor::vendors.bulk_select_at_least_one'),
                ]);
            }

            $action = $request->action;
            $message = '';

            switch ($action) {
                case 'delete':
                    Vendor::whereIn('id', $ids)->delete();
                    $message = trans('vendor::vendors.bulk_deleted_successfully');
                    break;
                case 'restore':
                    Vendor::withTrashed()->whereIn('id', $ids)->restore();
                    $message = trans('vendor::vendors.bulk_restored_successfully');
                    break;
                case 'force_delete':
                    Vendor::withTrashed()->whereIn('id', $ids)->forceDelete();
                    $message = trans('vendor::vendors.bulk_force_deleted_successfully');
                    break;
                case 'status':
                    $status = $request->status;
                    Vendor::whereIn('id', $ids)->update(['status' => $status]);
                    $message = trans('vendor::vendors.bulk_status_updated_successfully');
                    break;
                default:
                    throw new \InvalidArgumentException(trans('dashboard/general.invalid_action'));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
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
            'hasTrashed' => Vendor::onlyTrashed()->exists(),
        ]);
    }
}