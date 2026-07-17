<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Employee;

class EmployeeObserver {
    public function created(Employee $employee): void {
        $user = get_user_data();
        $employee->profile()->create([]);
        $employee->company_id = $employee->company_id ?? $user?->company_id;
        $employee->created_by = $user?->id;
    }

    public function updating(Employee $employee) {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $employee->company_id = $user->company_id;
        }
    }
}