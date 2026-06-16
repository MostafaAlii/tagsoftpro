<?php

namespace App\Observers;

use App\Models\Module;

class ModuleObserver
{
    public function creating(Module $module)
    {
        $user = get_user_data();
        $module->company_id = $module->company_id ?? $user?->company_id;
        $module->created_by = $user?->id;
    }

    public function updating(Module $module)
    {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $module->company_id = $user->company_id;
        }
    }
}