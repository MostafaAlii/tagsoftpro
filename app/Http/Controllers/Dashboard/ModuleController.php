<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\ModuleDataTable;
use App\Repositories\Contracts\ModuleRepositoryInterface;
use App\Models\Module;
use App\Http\Requests\Dashboard\Module\StoreModuleRequest;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function __construct(
        protected ModuleDataTable $moduleDataTable,
        protected ModuleRepositoryInterface $moduleInterface
    ) {}

    public function index()
    {
        return $this->moduleInterface->index($this->moduleDataTable);
    }

    public function store(StoreModuleRequest $request)
    {
        return $this->moduleInterface->store($request);
    }

    public function edit(Module $module)
    {
        $module->load(['translations', 'projectTypes']);
        return response()->json([
            'success' => true,
            'data' => $module,
        ]);
    }

    public function update(Request $request, Module $module)
    {
        return $this->moduleInterface->update($module, $request->all());
    }

    public function toggleStatus(Module $module)
    {
        return $this->moduleInterface->toggleStatus($module);
    }

    public function destroy(Module $module)
    {
        return $this->moduleInterface->destroy($module);
    }
}