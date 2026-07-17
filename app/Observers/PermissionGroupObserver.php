<?php

declare(strict_types=1);
namespace App\Observers;
use App\Models\PermissionGroup;
class PermissionGroupObserver
{
    public function creating(PermissionGroup $permissionGroup): void
    {
        $user = get_user_data();
        $permissionGroup->company_id = $permissionGroup->company_id ?? $user?->company_id;
        $permissionGroup->created_by = $user?->id;
    }

    public function updating(PermissionGroup $permissionGroup): void
    {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $permissionGroup->company_id = $user->company_id;
        }
        $permissionGroup->updated_by = $user?->id;
    }

    public function restoring(PermissionGroup $permissionGroup): void
    {
        $user = get_user_data();
        $permissionGroup->updated_by = $user?->id;
    }

    public function forceDeleted(PermissionGroup $permissionGroup): void
    {
        $permissionGroup->translations()->delete();
    }
}