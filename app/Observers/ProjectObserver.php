<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    public function creating(Project $projectType)
    {
        $user = get_user_data();
        $projectType->company_id = $projectType->company_id ?? $user?->company_id;
        $projectType->created_by = $user?->id;
    }

    public function updating(Project $projectType)
    {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $projectType->company_id = $user->company_id;
        }
    }
}