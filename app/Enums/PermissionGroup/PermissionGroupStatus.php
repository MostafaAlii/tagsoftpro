<?php

namespace App\Enums\PermissionGroup;

enum PermissionGroupStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVE => '<span class="badge bg-success">' . trans('dashboard/permission_groups.status_active') . '</span>',
            self::INACTIVE => '<span class="badge bg-danger">' . trans('dashboard/permission_groups.status_inactive') . '</span>',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::ACTIVE->value => trans('dashboard/permission_groups.status_active'),
            self::INACTIVE->value => trans('dashboard/permission_groups.status_inactive'),
        ];
    }
}