<?php

namespace App\Repositories\Eloquents;

use App\DataTables\Dashboard\Admin\ThemeDataTable;
use App\Repositories\Contracts\ThemeRepositoryInterface;
use App\Models\{Theme, ProjectType, Company};
use App\Http\Requests\Dashboard\Theme\StoreThemeRequest;
use App\Enums\Theme\ThemePaidType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Theme\ThemeResolver;
class ThemeRepository implements ThemeRepositoryInterface {
    public function index(ThemeDataTable $themeDataTable) {
        $projectTypes = ProjectType::active()->get();
        $companies = Company::whereStatus('active')->get(['id', 'name']);
        return $themeDataTable->render('dashboard.admin.themes.index', [
            'title' => trans('dashboard/themes.themes'),
            'projectTypes' => $projectTypes,
            'companies' => $companies,
        ]);
    }

    public function store(StoreThemeRequest $request) {
        try {
            DB::beginTransaction();
            $theme = Theme::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'paid_type' => $request->paid_type,
                'price' => $request->paid_type === 'paid' ? $request->price : null,
            ]);
            $projectTypes = $request->project_types ?? [];
            foreach ($projectTypes as $projectTypeId => $data) {
                $theme->projectTypes()->attach($projectTypeId, [
                    'is_active' => $data['is_active'] ?? true,
                    'is_default' => $data['is_default'] ?? false,
                    'created_by' => get_user_data()?->id,
                ]);
            }
            $this->ensureSingleDefaultPerProjectType($request->project_types, $theme->id);
            foreach (array_keys($projectTypes) as $projectTypeId) {
                ThemeResolver::forgetProjectTypeDefault((int) $projectTypeId);
            }
            DB::commit();
            return redirect()->route('admin.themes.index')->with('success', trans('dashboard/themes.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.themes.index')->with('error', trans('dashboard/general.error_occurred') . ': ' . $e->getMessage());
        }
    }

    public function edit(Theme $theme) {
        $theme->load('projectTypes');
        $data = $theme->toArray();
        if (isset($data['project_types'])) {
            foreach ($data['project_types'] as &$pt) {
                $pt['translated_name'] = $pt['name'] ?? $pt['id'];
            }
        }
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(Theme $theme, array $data) {
        try {
            DB::beginTransaction();
            $updateData = [
                'name' => $data['name'] ?? $theme->name,
                'code' => $data['code'] ?? $theme->code,
                'description' => $data['description'] ?? $theme->description,
                'paid_type' => $data['paid_type'] ?? $theme->paid_type,
                'company_id' => $data['company_id'] ?? $theme->company_id,
                'updated_by' => get_user_data()?->id,
            ];

            if (($data['paid_type'] ?? $theme->paid_type) === 'paid') {
                $updateData['price'] = $data['price'] ?? $theme->price;
            } else {
                $updateData['price'] = null;
            }
            $theme->update($updateData);
            if (isset($data['project_types']) && is_array($data['project_types'])) {
                $syncData = [];
                foreach ($data['project_types'] as $projectTypeId => $pivotData) {
                    if (is_array($pivotData)) {
                        $syncData[$projectTypeId] = [
                            'is_active' => isset($pivotData['is_active']) ? (bool)$pivotData['is_active'] : true,
                            'is_default' => isset($pivotData['is_default']) ? (bool)$pivotData['is_default'] : false,
                            'updated_by' => auth()->id(),
                        ];
                    }
                }
                if (!empty($syncData)) {
                    $theme->projectTypes()->sync($syncData);
                    $this->ensureSingleDefaultPerProjectType($data['project_types'], $theme->id);
                    foreach (array_keys($syncData) as $projectTypeId) {
                        ThemeResolver::forgetProjectTypeDefault((int) $projectTypeId);
                    }
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/themes.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    private function ensureSingleDefaultPerProjectType(array $projectTypes, int $themeId): void {
        foreach ($projectTypes as $projectTypeId => $data) {
            if (!is_array($data)) continue;
            
            $isDefault = isset($data['is_default']) ? (bool)$data['is_default'] : false;
            
            if ($isDefault) {
                DB::table('project_type_theme')
                    ->where('project_type_id', $projectTypeId)
                    ->where('theme_id', '!=', $themeId)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
                DB::table('project_type_theme')
                    ->where('project_type_id', $projectTypeId)
                    ->where('theme_id', $themeId)
                    ->update(['is_default' => true]);
            }
        }
    }

    public function toggleStatus(Theme $theme, int $projectTypeId) {
        try {
            $pivot = $theme->projectTypes()->where('project_type_id', $projectTypeId)->first(); 
            if (!$pivot) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/themes.not_associated'),
                ], 422);
            }
            $newStatus = !$pivot->pivot->is_active;
            if ($pivot->pivot->is_default && $newStatus === false) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/themes.cannot_deactivate_default'),
                ], 422);
            }
            $theme->projectTypes()->updateExistingPivot($projectTypeId, [
                'is_active' => $newStatus,
                'updated_by' => auth()->id(),
            ]);
            ThemeResolver::forgetProjectTypeDefault($projectTypeId);
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/themes.status_updated'),
                'is_active' => $newStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function toggleDefault(Theme $theme, int $projectTypeId) {
        try {
            DB::beginTransaction();
            DB::table('project_type_theme')->where('project_type_id', $projectTypeId)->update(['is_default' => false]);
            $theme->projectTypes()->updateExistingPivot($projectTypeId, [
                'is_default' => true,
                'updated_by' => get_user_data()->id,
            ]);
            DB::commit();
            ThemeResolver::forgetProjectTypeDefault($projectTypeId);
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/themes.default_updated'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function destroy(Theme $theme) {
        try {
            $hasDefault = $theme->projectTypes()->wherePivot('is_default', true)->exists();
            if ($hasDefault) {
                return response()->json([
                    'success' => false,
                    'message' => trans('dashboard/themes.cannot_delete_default'),
                ], 422);
            }
            $theme->delete();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/themes.deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }

    public function bulkUpdate(Theme $theme, array $data) {
        try {
            DB::beginTransaction();

            $type = $data['type'];
            $projectTypes = $data['project_types'] ?? [];

            foreach ($projectTypes as $projectTypeId => $pivotData) {
                $pivot = $theme->projectTypes()->where('project_type_id', $projectTypeId)->first();
                if (!$pivot) continue;

                if ($type === 'default') {
                    $theme->projectTypes()->updateExistingPivot($projectTypeId, [
                        'is_default' => (bool)($pivotData['is_default'] ?? false),
                        'updated_by' => auth()->id(),
                    ]);
                } elseif ($type === 'status') {
                    if ($pivot->pivot->is_default && !($pivotData['is_active'] ?? true)) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => trans('dashboard/themes.cannot_deactivate_default'),
                        ], 422);
                    }
                    $theme->projectTypes()->updateExistingPivot($projectTypeId, [
                        'is_active' => (bool)($pivotData['is_active'] ?? true),
                        'updated_by' => auth()->id(),
                    ]);
                }
                $affectedProjectTypeIds[] = (int) $projectTypeId;
            }
            foreach ($affectedProjectTypeIds as $projectTypeId) {
                ThemeResolver::forgetProjectTypeDefault($projectTypeId);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('dashboard/themes.updated_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => trans('dashboard/general.error_occurred'),
            ], 500);
        }
    }
}