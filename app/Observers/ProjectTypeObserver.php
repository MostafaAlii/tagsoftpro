<?php

namespace App\Observers;

use App\Models\ProjectType;

class ProjectTypeObserver
{
    public function creating(ProjectType $projectType)
    {
        $user = get_user_data();
        $projectType->company_id = $projectType->company_id ?? $user?->company_id;
        $projectType->created_by = $user?->id;
    }

    public function updating(ProjectType $projectType)
    {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $projectType->company_id = $user->company_id;
        }
    }
}