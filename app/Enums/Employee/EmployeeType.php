<?php

namespace App\Enums\Employee;

enum EmployeeType: string
{
    case FULL_TIME  = 'full_time';
    case PART_TIME  = 'part_time';
    case CONTRACTOR = 'contractor';
    case INTERN     = 'intern';
    case REMOTE     = 'remote';

    public function label(): string
    {
        return match ($this) {
            self::FULL_TIME  => trans('dashboard/employees.type_full_time'),
            self::PART_TIME  => trans('dashboard/employees.type_part_time'),
            self::CONTRACTOR => trans('dashboard/employees.type_contractor'),
            self::INTERN     => trans('dashboard/employees.type_intern'),
            self::REMOTE     => trans('dashboard/employees.type_remote'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::FULL_TIME  => '<span class="badge bg-primary">' . $this->label() . '</span>',
            self::PART_TIME  => '<span class="badge bg-info">' . $this->label() . '</span>',
            self::CONTRACTOR => '<span class="badge bg-warning">' . $this->label() . '</span>',
            self::INTERN     => '<span class="badge bg-secondary">' . $this->label() . '</span>',
            self::REMOTE     => '<span class="badge bg-success">' . $this->label() . '</span>',
        };
    }
}
