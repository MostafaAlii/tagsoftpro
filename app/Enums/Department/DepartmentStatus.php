<?php

namespace App\Enums\Department;

enum DepartmentStatus: int
{
    case ACTIVE   = 1;
    case INACTIVE = 0;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE   => trans('dashboard/general.active'),
            self::INACTIVE => trans('dashboard/general.in_active'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVE   => '<span class="badge bg-success">' . $this->label() . '</span>',
            self::INACTIVE => '<span class="badge bg-danger">' . $this->label() . '</span>',
        };
    }
}