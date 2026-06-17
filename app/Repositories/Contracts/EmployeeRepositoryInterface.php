<?php

namespace App\Repositories\Contracts;

use App\DataTables\Dashboard\Admin\EmployeeDataTable;
use App\Models\Employee;
use App\Http\Requests\Dashboard\Employee\StoreEmployeeRequest;

interface EmployeeRepositoryInterface
{
    public function index(EmployeeDataTable $employeeDataTable);
    public function store(StoreEmployeeRequest $request);
    public function update(Employee $employee, array $data);
    public function edit(Employee $employee);
    public function toggleStatus(Employee $employee);
    public function destroy(Employee $employee);
    public function restore($id);
    public function forceDelete($id);
}
