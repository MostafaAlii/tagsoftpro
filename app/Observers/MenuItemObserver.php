<?php
namespace App\Observers;
use App\Models\MenuItem;
class MenuItemObserver {
    public function creating(MenuItem $menuItem): void {
        $user = get_user_data();
        $menuItem->company_id = $menuItem->company_id ?? $user?->company_id;
        $menuItem->created_by = $user?->id;
        if (empty($menuItem->type)) {
            $menuItem->type = 'link';
        }
        if (empty($menuItem->status)) {
            $menuItem->status = 'active';
        }
        if (empty($menuItem->target)) {
            $menuItem->target = '_self';
        }
    }

    public function updating(MenuItem $menuItem): void {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $menuItem->company_id = $user->company_id;
        }
        $menuItem->updated_by = $user?->id;
    }

    public function restoring(MenuItem $menuItem): void {
        $user = get_user_data();
        $menuItem->updated_by = $user?->id;
    }

    public function forceDeleted(MenuItem $menuItem): void {
        $menuItem->translations()->delete();
    }
}