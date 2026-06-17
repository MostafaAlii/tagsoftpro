<?php

namespace App\Enums\Employee;

enum EmployeeStatus: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case ON_LEAVE = 'on_leave';
    case TERMINATED = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE     => trans('dashboard/employees.status_active'),
            self::INACTIVE   => trans('dashboard/employees.status_inactive'),
            self::ON_LEAVE   => trans('dashboard/employees.status_on_leave'),
            self::TERMINATED => trans('dashboard/employees.status_terminated'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVE     => '<span class="badge bg-success">' . $this->label() . '</span>',
            self::INACTIVE   => '<span class="badge bg-danger">' . $this->label() . '</span>',
            self::ON_LEAVE   => '<span class="badge bg-warning">' . $this->label() . '</span>',
            self::TERMINATED => '<span class="badge bg-secondary">' . $this->label() . '</span>',
        };
    }
}
