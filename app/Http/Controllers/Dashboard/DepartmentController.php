<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\DepartmentDataTable;
use App\Repositories\Contracts\DepartmentRepositoryInterface;
use App\Models\Department;
use App\Http\Requests\Dashboard\Department\StoreDepartmentRequest;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentDataTable $departmentDataTable,
        protected DepartmentRepositoryInterface $departmentInterface
    ) {}

    public function index()
    {
        return $this->departmentInterface->index($this->departmentDataTable);
    }

    public function store(StoreDepartmentRequest $request)
    {
        return $this->departmentInterface->store($request);
    }

    public function edit(Department $department)
    {
        return $this->departmentInterface->edit($department);
    }

    public function update(Request $request, Department $department)
    {
        return $this->departmentInterface->update($department, $request->all());
    }

    public function toggleStatus(Department $department)
    {
        return $this->departmentInterface->toggleStatus($department);
    }

    public function destroy(Department $department)
    {
        return $this->departmentInterface->destroy($department);
    }
}