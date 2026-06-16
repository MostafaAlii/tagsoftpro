<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\ProjectDataTable;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Models\{Project, Company, ProjectType, Module};
use App\Http\Requests\Dashboard\Project\StoreProjectRequest;
use App\Models\Concerns\UploadMedia;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectRepositoryInterface
{
    use UploadMedia;
    public function index(ProjectDataTable $projectDataTable)
    {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $projectTypes = ProjectType::active()->with('translations')->get();
        $modules = Module::active()->with('translations')->get();
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        return $projectDataTable->render('dashboard.admin.projects.index', [
            'title'        => trans('dashboard/projects.projects'),
            'companies'    => $companies,
            'projectTypes' => $projectTypes,
            'modules'      => $modules,
            'locales'      => $locales,
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        try {
            DB::beginTransaction();

            $project = Project::create([
                'status'     => $request->status,
                'company_id' => $request->company_id,
                'created_by' => auth()->id(),
            ]);

            // Translations
            foreach ($request->name as $locale => $name) {
                if (filled($name)) {
                    $project->translateOrNew($locale)->name = $name;
                    $project->translateOrNew($locale)->description = $request->description[$locale] ?? null;
                }
            }
            $project->save();

            // ✅ ربط Project Types
            if ($request->has('project_types')) {
                $project->projectTypes()->sync($request->project_types);
            }

            // ✅ ربط Modules
            if ($request->has('modules')) {
                $project->modules()->sync($request->modules);
            }

            if ($request->hasFile('project')) {
                $project->uploadSingleMedia('project', $request->file('project'), $project, null, 'media', true, false, 'project');
            }

            DB::commit();

            return redirect()->route('admin.projects.index')
                ->with('success', trans('dashboard/projects.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.projects.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function edit(Project $project)
    {
        $project->load(['translations', 'projectTypes', 'modules', 'media']);
        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function update(Project $project, array $data, $request = null)
    {
        try {
            DB::beginTransaction();

            $project->update([
                'status'     => $data['status'] ?? $project->status,
            ]);

            // Translations
            if (isset($data['name'])) {
                foreach ($data['name'] as $locale => $name) {
                    if (filled($name)) {
                        $project->translateOrNew($locale)->name = $name;
                        $project->translateOrNew($locale)->description = $data['description'][$locale] ?? null;
                    }
                }
            }
            $project->save();

            // ✅ تحديث Project Types
            if (isset($data['project_types']) && !empty($data['project_types'])) {
                $projectTypes = explode(',', $data['project_types']);
                $projectTypes = array_map('intval', $projectTypes);
                $project->projectTypes()->sync($projectTypes);
            } else {
                $project->projectTypes()->detach();
            }

            // ✅ تحديث Modules
            if (isset($data['modules']) && !empty($data['modules'])) {
                $modules = explode(',', $data['modules']);
                $modules = array_map('intval', $modules);
                $project->modules()->sync($modules);
            } else {
                $project->modules()->detach();
            }

            if ($request && $request->hasFile('project')) {
                $project->updateSingleMedia('project',
                    $request->file('project'),
                    $project,
                    null,
                    'media',
                    true,
                    false,
                    'project'
                );
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/projects.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(Project $project)
    {
        try {
            // تبديل بين الحالات
            $statuses = ['active', 'inactive', 'published', 'draft'];
            $currentIndex = array_search($project->status->value, $statuses);
            $nextIndex = ($currentIndex + 1) % count($statuses);
            $newStatus = $statuses[$nextIndex];

            $project->update([
                'status' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $project->status->badge(),
                'message' => trans('dashboard/projects.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Project $project)
    {
        try {
            $this->deleteExistingMedia(
                'project',
                $project,
                null,        // no column
                'media',     // relation name
                true,        // useStorage (direct_public)
                'project'    // collection_name
            );
            $project->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/projects.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.projects.index')
                ->with('success', trans('dashboard/projects.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.projects.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }
}