<?php

namespace App\Enums\Admin;

enum AdminType: string
{
    case OWNER = 'owner';
    case COMPANY_ADMIN = 'company_admin';
    case BRANCH_ADMIN = 'branch_admin';

    public function isOwner(): bool
    {
        return $this === self::OWNER;
    }

    public function isCompanyAdmin(): bool
    {
        return $this === self::COMPANY_ADMIN;
    }

    public function isBranchAdmin(): bool
    {
        return $this === self::BRANCH_ADMIN;
    }

    public static function label($value): string {
        return match ($value) {
            self::OWNER => trans('dashboard/general.owner'),
            self::COMPANY_ADMIN => trans('dashboard/general.company_admin'),
            self::BRANCH_ADMIN => trans('dashboard/general.branch_admin'),
            default => trans('dashboard/general.not_type_assigned'),
        };
    }

    public static function badge($value): string
    {
        return match ($value) {
            self::OWNER => '<span class="badge bg-primary">' . trans('dashboard/general.owner') . '</span>',
            self::COMPANY_ADMIN => '<span class="badge bg-info">' . trans('dashboard/general.company_admin') . '</span>',
            self::BRANCH_ADMIN => '<span class="badge bg-secondary">' . trans('dashboard/general.branch_admin') . '</span>',
            default => '<span class="badge bg-default">' . trans('dashboard/general.not_type_assigned') . '</span>',
        };
    }
}