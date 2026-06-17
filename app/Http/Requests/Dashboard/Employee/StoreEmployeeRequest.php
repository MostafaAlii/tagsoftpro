<?php

namespace App\Http\Requests\Dashboard\Employee;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Employee\{EmployeeStatus, EmployeeType};

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employeeId,
            'phone' => 'nullable|string|max:30',
            'status' => 'required|in:' . implode(',', array_column(EmployeeStatus::cases(), 'value')),
            'type' => 'required|in:' . implode(',', array_column(EmployeeType::cases(), 'value')),
            'password' => $employeeId ? 'nullable|min:8' : 'required|min:8',
            'date' => 'nullable|date',
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('dashboard/employees.name'),
            'email' => trans('dashboard/employees.email'),
            'phone' => trans('dashboard/employees.phone'),
            'status' => trans('dashboard/employees.status'),
            'type' => trans('dashboard/employees.type'),
            'password' => trans('dashboard/employees.password'),
            'date' => trans('dashboard/employees.date'),
            'company_id' => trans('dashboard/employees.company'),
            'department_id' => trans('dashboard/employees.department'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('dashboard/employees.validation.name_required'),
            'email.required' => trans('dashboard/employees.validation.email_required'),
            'email.unique' => trans('dashboard/employees.validation.email_unique'),
            'status.required' => trans('dashboard/employees.validation.status_required'),
            'status.in' => trans('dashboard/employees.validation.status_in'),
            'type.required' => trans('dashboard/employees.validation.type_required'),
            'type.in' => trans('dashboard/employees.validation.type_in'),
            'password.required' => trans('dashboard/employees.validation.password_required'),
            'password.min' => trans('dashboard/employees.validation.password_min'),
        ];
    }
}