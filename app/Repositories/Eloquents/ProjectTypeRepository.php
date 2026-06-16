<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\ProjectTypeDataTable;
use App\Repositories\Contracts\ProjectTypeRepositoryInterface;
use App\Models\{ProjectType, Company};
use App\Http\Requests\Dashboard\ProjectType\StoreProjectTypeRequest;
use App\Enums\ProjectType\ProjectTypeStatus;
use Illuminate\Support\Facades\DB;

class ProjectTypeRepository implements ProjectTypeRepositoryInterface
{
    public function index(ProjectTypeDataTable $projectTypeDataTable)
    {
        $companies = Company::whereStatus('active')->get(['id', 'name']);

        return $projectTypeDataTable->render('dashboard.admin.projectTypes.index', [
            'title'     => trans('dashboard/project_types.project_types'),
            'companies' => $companies,
        ]);
    }

    public function store(StoreProjectTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            $projectType = ProjectType::create([
                'status'     => $request->boolean('status'),
                'company_id' => $request->company_id,
                'created_by' => auth()->id(),
            ]);

            // Translations
            foreach ($request->name as $locale => $name) {
                if (filled($name)) {
                    $projectType->translateOrNew($locale)->name = $name;
                    $projectType->translateOrNew($locale)->description = $request->description[$locale] ?? null;
                }
            }
            $projectType->save();

            DB::commit();

            return redirect()->route('admin.projectTypes.index')
                ->with('success', trans('dashboard/project_types.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.projectTypes.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function update(ProjectType $projectType, array $data)
    {
        try {
            DB::beginTransaction();

            $projectType->update([
                'status'     => $data['status'] ?? $projectType->status,
                'company_id' => $data['company_id'] ?? $projectType->company_id,
                'updated_by' => auth()->id(),
            ]);

            // Translations
            if (isset($data['name'])) {
                foreach ($data['name'] as $locale => $name) {
                    if (filled($name)) {
                        $projectType->translateOrNew($locale)->name = $name;
                        $projectType->translateOrNew($locale)->description = $data['description'][$locale] ?? null;
                    }
                }
            }
            $projectType->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/project_types.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(ProjectType $projectType)
    {
        try {
            $projectType->update([
                'status' => $projectType->status === ProjectTypeStatus::ACTIVE
                    ? ProjectTypeStatus::INACTIVE
                    : ProjectTypeStatus::ACTIVE,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $projectType->status->badge(),
                'message' => trans('dashboard/project_types.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(ProjectType $projectType)
    {
        try {
            $projectType->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/project_types.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.projectTypes.index')
                ->with('success', trans('dashboard/project_types.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.projectTypes.index')->with('error', trans('dashboard/general.error_occurred'));
        }
    }
}