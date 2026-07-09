<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\ThemeDataTable;
use App\Repositories\Contracts\ThemeRepositoryInterface;
use App\Models\Theme;
use App\Http\Requests\Dashboard\Theme\StoreThemeRequest;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function __construct(
        protected ThemeDataTable $themeDataTable,
        protected ThemeRepositoryInterface $themeInterface
    ) {}

    public function index()
    {
        return $this->themeInterface->index($this->themeDataTable);
    }

    public function store(StoreThemeRequest $request)
    {
        return $this->themeInterface->store($request);
    }

    public function edit(Theme $theme)
    {
        return $this->themeInterface->edit($theme);
    }

    public function update(Request $request, Theme $theme)
    {
        return $this->themeInterface->update($theme, $request->all());
    }

    public function toggleStatus(Request $request, Theme $theme, int $projectTypeId)
    {
        return $this->themeInterface->toggleStatus($theme, $projectTypeId);
    }

    public function toggleDefault(Request $request, Theme $theme, int $projectTypeId)
    {
        return $this->themeInterface->toggleDefault($theme, $projectTypeId);
    }

    public function destroy(Theme $theme)
    {
        return $this->themeInterface->destroy($theme);
    }

    public function getStatuses(Theme $theme) {
        $data = $theme->projectTypes->map(function ($type) use ($theme) {
            return [
                'id' => $type->id,
                'name' => $type->getTranslatedName(),
                'theme_id' => $theme->id,
                'is_active' => (bool) $type->pivot->is_active,
                'is_default' => (bool) $type->pivot->is_default,
                'toggle_status_route' => route('admin.themes.toggleStatus', [
                    'theme' => $theme->id, 
                    'projectType' => $type->id
                ]),
                'toggle_default_route' => route('admin.themes.toggleDefault', [
                    'theme' => $theme->id, 
                    'projectType' => $type->id
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function bulkUpdate(Theme $theme, Request $request) {
        return $this->themeInterface->bulkUpdate($theme, $request->all());
    }
}