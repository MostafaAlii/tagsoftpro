<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\ModuleDataTable;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use App\Models\{Module, Company, ProjectType};
use App\Http\Requests\Dashboard\Module\StoreModuleRequest;
use App\Enums\Module\ModuleStatus;
use Illuminate\Support\Facades\DB;

class ModuleRepository implements ModuleRepositoryInterface
{
    public function index(ModuleDataTable $moduleDataTable)
    {
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        $projectTypes = ProjectType::active()->with('translations')->get();
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        return $moduleDataTable->render('dashboard.admin.modules.index', [
            'title'     => trans('dashboard/modules.modules'),
            'companies' => $companies,
            'projectTypes' => $projectTypes,
            'locales'      => $locales,
        ]);
    }

    public function store(StoreModuleRequest $request)
    {
        try {
            DB::beginTransaction();
            $module = Module::create([
                'status'     => $request->boolean('status'),
            ]);

            // Translations
            foreach ($request->name as $locale => $name) {
                if (filled($name)) {
                    $module->translateOrNew($locale)->name = $name;
                    $module->translateOrNew($locale)->description = $request->description[$locale] ?? null;
                }
            }
            $module->save();
            if ($request->has('project_types')) {
                $module->projectTypes()->sync($request->project_types);
            }
            DB::commit();

            return redirect()->route('admin.modules.index')
                ->with('success', trans('dashboard/modules.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.modules.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }

    public function update(Module $module, array $data)
    {
        try {
            DB::beginTransaction();

            $module->update([
                'status'     => $data['status'] ?? $module->status,
            ]);

            // Translations
            if (isset($data['name'])) {
                foreach ($data['name'] as $locale => $name) {
                    if (filled($name)) {
                        $module->translateOrNew($locale)->name = $name;
                        $module->translateOrNew($locale)->description = $data['description'][$locale] ?? null;
                    }
                }
            }
            $module->save();
            if (isset($data['project_types'])) {
                $projectTypes = is_string($data['project_types'])
                    ? explode(',', $data['project_types'])
                    : $data['project_types'];
                $projectTypes = array_map('intval', $projectTypes);
                $module->projectTypes()->sync($projectTypes);
            } else {
                $module->projectTypes()->detach();
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('dashboard/modules.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleStatus(Module $module)
    {
        try {
            $module->update([
                'status' => $module->status === ModuleStatus::ACTIVE
                    ? ModuleStatus::INACTIVE
                    : ModuleStatus::ACTIVE,
            ]);

            return response()->json([
                'success' => true,
                'badge'   => $module->status->badge(),
                'message' => trans('dashboard/modules.status_updated'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Module $module)
    {
        try {
            $module->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('dashboard/modules.deleted_successfully'),
                ]);
            }

            return redirect()->route('admin.modules.index')
                ->with('success', trans('dashboard/modules.deleted_successfully'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/general.error_occurred'),
                ], 500);
            }

            return redirect()->route('admin.modules.index')
                ->with('error', trans('dashboard/general.error_occurred'));
        }
    }
}