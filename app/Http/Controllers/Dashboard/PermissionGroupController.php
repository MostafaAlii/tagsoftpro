<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\PermissionGroupDataTable;
use App\Repositories\Contracts\PermissionGroupRepositoryInterface;
use App\Http\Requests\Dashboard\Permission\PermissionGroup\StorePermissionGroupRequest;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

class PermissionGroupController extends Controller
{
    protected $permissionGroupRepository;

    public function __construct(PermissionGroupRepositoryInterface $permissionGroupRepository)
    {
        $this->permissionGroupRepository = $permissionGroupRepository;
    }

    public function index(PermissionGroupDataTable $permissionGroupDataTable)
    {
        return $this->permissionGroupRepository->index($permissionGroupDataTable);
    }

    public function store(StorePermissionGroupRequest $request)
    {
        return $this->permissionGroupRepository->store($request);
    }

    public function edit(PermissionGroup $permissionGroup)
    {
        return $this->permissionGroupRepository->edit($permissionGroup);
    }

    public function update(Request $request, PermissionGroup $permissionGroup)
    {
        return $this->permissionGroupRepository->update($permissionGroup, $request->all(), $request);
    }

    public function toggleStatus(PermissionGroup $permissionGroup)
    {
        return $this->permissionGroupRepository->toggleStatus($permissionGroup);
    }

    public function destroy(PermissionGroup $permissionGroup)
    {
        return $this->permissionGroupRepository->destroy($permissionGroup);
    }

    public function restore($id)
    {
        return $this->permissionGroupRepository->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->permissionGroupRepository->forceDelete($id);
    }

    public function bulkAction(Request $request)
    {
        return $this->permissionGroupRepository->bulkAction($request);
    }

    public function hasTrashed()
    {
        return $this->permissionGroupRepository->hasTrashed();
    }
}