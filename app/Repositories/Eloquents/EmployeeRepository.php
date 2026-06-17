<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\EmployeeDataTable;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Models\{Employee, Company, Department};
use App\Http\Requests\Dashboard\Employee\StoreEmployeeRequest;
use Illuminate\Support\Facades\{DB,Hash};
use Illuminate\Http\Request;
use App\Models\Concerns\UploadMedia;
class EmployeeRepository implements EmployeeRepositoryInterface {
    use UploadMedia;
    public function index(EmployeeDataTable $employeeDataTable) {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $departments = Department::active()->with('translations')->get();
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        return $employeeDataTable->render('dashboard.admin.employees.index', [
            'title'       => trans('dashboard/employees.employees'),
            'companies'   => $companies,
            'departments' => $departments,
            'locales'     => $locales,
        ]);
    }

    public function store(StoreEmployeeRequest $request) {
        try {
            DB::beginTransaction();
            $employee = Employee::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'status'    => $request->status,
                'type'      => $request->type,
                'password'  => Hash::make($request->password),
                'date'      => $request->date,
                'company_id' => $request->company_id,
                'department_id' => $request->department_id,
            ]);
            if ($request->hasFile('employee')) {
                $employee->uploadSingleMedia(
                    'employee',
                    $request->file('employee'),
                    $employee,
                    null,
                    'media',
                    true,
                    false,
                    'employee'
                );
            }
            DB::commit();
            return redirect()->route('admin.employees.index')
                ->with('success', trans('dashboard/employees.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.employees.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function edit(Employee $employee) {
        $employee->load('media');
        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    public function update(Employee $employee, array $data, ?Request $request = null) {
        try {
            DB::beginTransaction();
            $updateData = [
                'name'      => $data['name'] ?? $employee->name,
                'email'     => $data['email'] ?? $employee->email,
                'phone'     => $data['phone'] ?? $employee->phone,
                'status'    => $data['status'] ?? $employee->status,
                'type'      => $data['type'] ?? $employee->type,
                'date'      => $data['date'] ?? $employee->date,
                'company_id' => $data['company_id'] ?? $employee->company_id,
                'department_id' => $data['department_id'] ?? $employee->department_id,
            ];
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }
            $employee->update($updateData);
            if ($request && $request->hasFile('employee')) {
                $employee->updateSingleMedia(
                    'employee',
                    $request->file('employee'),
                    $employee,
                    null,
                    'media',
                    true,
                    false,
                    'employee'
                );
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/employees.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(Employee $employee) {
        try {
            $statuses = ['active', 'inactive', 'on_leave', 'terminated'];
            $currentIndex = array_search($employee->status->value, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $newStatus = $statuses[$nextIndex];
            $employee->update([
                'status' => $newStatus,
            ]);
            return response()->json([
                'success' => true,
                'badge'   => $employee->status->badge(),
                'message' => trans('dashboard/employees.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Employee $employee) {
        try {
            $employee->deleteExistingMedia(
                'employee',
                $employee,
                null,
                'media',
                true,
                'employee'
            );
            $employee->delete();
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/employees.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.employees.index')
                ->with('success', trans('dashboard/employees.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.employees.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function restore($id) {
        try {
            $employee = Employee::withTrashed()->findOrFail($id);
            $employee->restore();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/employees.restored_successfully'),
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
            $employee = Employee::withTrashed()->findOrFail($id);
            $this->deleteExistingMedia(
                'employee',
                $employee,
                null,
                'media',
                true,
                'employee'
            );
            $employee->forceDelete();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/employees.permanently_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function bulkAction(Request $request) {
        
        try {
            $ids = $request->ids;
            $action = $request->action;
            $status = $request->status;

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/employees.bulk_select_at_least_one'),
                ]);
            }

            switch ($action) {
                case 'status':
                    Employee::whereIn('id', $ids)->update(['status' => $status]);
                    $message = trans('dashboard/employees.bulk_status_updated');
                    break;
                case 'delete':
                    $employees = Employee::whereIn('id', $ids)->get();
                    foreach ($employees as $employee) {
                        $this->deleteExistingMedia('employee', $employee, null, 'media', true, 'employee');
                    }
                    Employee::whereIn('id', $ids)->delete();
                    $message = trans('dashboard/employees.bulk_deleted');
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => trans('dashboard/general.error_occurred'),
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
