<?php

namespace App\Enums\MainSetting;

enum MainSettingSystemStatus: string
{
    case SYSTEM_STATUS_ACTIVE = 'active';
    case SYSTEM_STATUS_IN_ACTIVE = 'inactive';

    public static function status($value): string
    {
        if ($value == self::SYSTEM_STATUS_ACTIVE) {
            return '<span class="badge badge-success">' . trans('dashboard/general.active') . '</span>';
        } elseif ($value == self::SYSTEM_STATUS_IN_ACTIVE) {
            return '<span class="badge badge-warning">' . trans('dashboard/general.in_active') . '</span>';
        } else {
            return '<span class="badge badge-default">' . trans('dashboard/general.not_status_assigned') . '</span>';
        }
    }
}
