<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\EmployeeDataTable;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Models\{Employee, Company, Department};
use App\Http\Requests\Dashboard\Employee\StoreEmployeeRequest;
use App\Enums\Employee\EmployeeStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function index(EmployeeDataTable $employeeDataTable)
    {
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

    public function store(StoreEmployeeRequest $request)
    {
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

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', trans('dashboard/employees.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.employees.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function edit(Employee $employee)
    {
        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    public function update(Employee $employee, array $data)
    {
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

            // إذا تم إرسال كلمة مرور جديدة
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $employee->update($updateData);

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

    public function toggleStatus(Employee $employee)
    {
        try {
            // تبديل الحالة
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

    public function destroy(Employee $employee)
    {
        try {
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
}
