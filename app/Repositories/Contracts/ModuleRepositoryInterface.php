<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\ModuleDataTable;
use App\Models\Module;
use App\Http\Requests\Dashboard\Module\StoreModuleRequest;

interface ModuleRepositoryInterface
{
    public function index(ModuleDataTable $moduleDataTable);
    public function store(StoreModuleRequest $request);
    public function update(Module $module, array $data);
    public function toggleStatus(Module $module);
    public function destroy(Module $module);
}