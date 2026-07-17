<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\PermissionGroupDataTable;
use App\Http\Requests\Dashboard\Permission\PermissionGroup\StorePermissionGroupRequest;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

interface PermissionGroupRepositoryInterface
{
    public function index(PermissionGroupDataTable $permissionGroupDataTable);
    public function store(StorePermissionGroupRequest $request);
    public function edit(PermissionGroup $permissionGroup);
    public function update(PermissionGroup $permissionGroup, array $data, ?Request $request = null);
    public function toggleStatus(PermissionGroup $permissionGroup);
    public function destroy(PermissionGroup $permissionGroup);
    public function restore($id);
    public function forceDelete($id);
    public function bulkAction(Request $request);
    public function hasTrashed();
}