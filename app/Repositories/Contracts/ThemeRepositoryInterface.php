<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\ThemeDataTable;
use App\Models\Theme;
use App\Http\Requests\Dashboard\Theme\StoreThemeRequest;

interface ThemeRepositoryInterface
{
    public function index(ThemeDataTable $themeDataTable);
    public function store(StoreThemeRequest $request);
    public function edit(Theme $theme);
    public function update(Theme $theme, array $data);
    public function toggleStatus(Theme $theme, int $projectTypeId);
    public function toggleDefault(Theme $theme, int $projectTypeId); 
    public function destroy(Theme $theme);
    public function bulkUpdate(Theme $theme, array $data);
}