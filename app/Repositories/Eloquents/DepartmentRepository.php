<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\DepartmentDataTable;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Models\{Department, Company};
use App\Http\Requests\Dashboard\Department\StoreDepartmentRequest;
use App\Enums\Department\DepartmentStatus;
use Illuminate\Support\Facades\DB;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function index(DepartmentDataTable $departmentDataTable)
    {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        return $departmentDataTable->render('dashboard.admin.departments.index', [
            'title'     => trans('dashboard/departments.departments'),
            'companies' => $companies,
            'locales'   => $locales,
        ]);
    }

    public function store(StoreDepartmentRequest $request)
    {
        try {
            DB::beginTransaction();

            $department = Department::create([
                'status'     => $request->boolean('status'),
                'company_id' => $request->company_id,
                'created_by' => auth()->id(),
            ]);

            // Translations
            foreach ($request->name as $locale => $name) {
                if (filled($name)) {
                    $department->translateOrNew($locale)->name = $name;
                    $department->translateOrNew($locale)->description = $request->description[$locale] ?? null;
                }
            }
            $department->save();

            DB::commit();

            return redirect()->route('admin.departments.index')
                ->with('success', trans('dashboard/departments.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.departments.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function edit(Department $department)
    {
        $department->load('translations');
        return response()->json([
            'success' => true,
            'data' => $department,
        ]);
    }

    public function update(Department $department, array $data)
    {
        try {
            DB::beginTransaction();

            $department->update([
                'status'     => $data['status'] ?? $department->status,
                'company_id' => $data['company_id'] ?? $department->company_id,
                'updated_by' => auth()->id(),
            ]);

            // Translations
            if (isset($data['name'])) {
                foreach ($data['name'] as $locale => $name) {
                    if (filled($name)) {
                        $department->translateOrNew($locale)->name = $name;
                        $department->translateOrNew($locale)->description = $data['description'][$locale] ?? null;
                    }
                }
            }
            $department->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/departments.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(Department $department)
    {
        try {
            $department->update([
                'status' => $department->status === DepartmentStatus::ACTIVE
                    ? DepartmentStatus::INACTIVE
                    : DepartmentStatus::ACTIVE,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $department->status->badge(),
                'message' => trans('dashboard/departments.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Department $department)
    {
        try {
            $department->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/departments.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.departments.index')
                ->with('success', trans('dashboard/departments.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.departments.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }
}