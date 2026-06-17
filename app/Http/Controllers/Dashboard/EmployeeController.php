<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\Dashboard\Admin\EmployeeDataTable;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Models\Employee;
use App\Http\Requests\Dashboard\Employee\StoreEmployeeRequest;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeDataTable $employeeDataTable,
        protected EmployeeRepositoryInterface $employeeInterface
    ) {}

    public function index()
    {
        return $this->employeeInterface->index($this->employeeDataTable);
    }

    public function store(StoreEmployeeRequest $request)
    {
        return $this->employeeInterface->store($request);
    }

    public function edit(Employee $employee)
    {
        return $this->employeeInterface->edit($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        return $this->employeeInterface->update($employee, $request->all(), $request);
    }

    public function toggleStatus(Employee $employee)
    {
        return $this->employeeInterface->toggleStatus($employee);
    }

    public function destroy(Employee $employee)
    {
        return $this->employeeInterface->destroy($employee);
    }
}