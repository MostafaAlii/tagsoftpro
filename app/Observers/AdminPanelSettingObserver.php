<?php

namespace App\Observers;

use App\Models\AdminPanelSetting;

class AdminPanelSettingObserver
{
    public function creating(AdminPanelSetting $setting)
    {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $setting->company_id = $user->company_id;
        }
    }

    public function updating(AdminPanelSetting $setting)
    {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $setting->company_id = $user->company_id;
        }
    }
}