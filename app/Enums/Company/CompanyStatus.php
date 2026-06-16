<?php

namespace App\Enums\Company;

enum CompanyStatus: string
{
    case ACTIVE = 'active';
    case IN_ACTIVE = 'inactive';
    case BLOCKED = "blocked";

    public static function options(): array {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => trans('dashboard/general.' . strtolower($case->name))
        ])->toArray();
    }

    public static function status($value): string
    {
        if ($value == self::ACTIVE) {
            return '<span class="badge badge-success">' . trans('general.active') . '</span>';
        } elseif ($value == self::IN_ACTIVE) {
            return '<span class="badge badge-warning">' . trans('general.in_active') . '</span>';
        } elseif ($value == self::BLOCKED) {
            return '<span class="badge badge-danger">' . trans('general.blocked') . '</span>';
        } else {
            return '<span class="badge badge-default">' . trans('general.not_status_assigned') . '</span>';
        }
    }

    public static function badge($value): string
    {
        return match ($value) {
            self::ACTIVE->value =>
            '<span class="badge badge-success text-success fs-6">
                    <i class="fa fa-check"></i> ' . trans('dashboard/general.active') . '
                </span>',

            self::IN_ACTIVE->value =>
            '<span class="badge badge-warning text-warning fs-6">
                    <i class="fas fa-times-circle"></i> ' . trans('dashboard/general.in_active') . '
                </span>',

            self::BLOCKED->value =>
            '<span class="badge badge-danger text-danger fs-6">
                    <i class="fas fa-times-circle"></i> ' . trans('dashboard/general.blocked') . '
                </span>',

            default =>
            '<span class="badge badge-secondary text-primary fs-6">' . trans('dashboard/general.not_status_assigned') . '</span>',
        };
    }

    public static function rowClass(string $value): string
    {
        return match ($value) {
            self::ACTIVE->value   => 'table-success',
            self::IN_ACTIVE->value => 'table-warning',
            self::BLOCKED->value  => 'table-danger',
            default               => '',
        };
    }
}