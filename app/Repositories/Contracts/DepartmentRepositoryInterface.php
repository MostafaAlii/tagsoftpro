<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\DepartmentDataTable;
use App\Models\Department;
use App\Http\Requests\Dashboard\Department\StoreDepartmentRequest;

interface DepartmentRepositoryInterface
{
    public function index(DepartmentDataTable $departmentDataTable);
    public function store(StoreDepartmentRequest $request);
    public function update(Department $department, array $data);
    public function edit(Department $department);
    public function toggleStatus(Department $department);
    public function destroy(Department $department);
}