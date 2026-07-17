<?php

namespace App\Observers;

use App\Models\Menu;
use Illuminate\Support\Str;

class MenuObserver {
    public function creating(Menu $menu): void {
        if (empty($menu->key)) {
            $menu->key = Str::slug($menu->name, '_');
        }
        $user = get_user_data();
        $menu->company_id = $menu->company_id ?? $user?->company_id;
        $menu->created_by = $user?->id;
    }

    public function updating(Menu $menu): void {
        $user = get_user_data();
        if ($user && $user->company_id) {
            $menu->company_id = $user->company_id;
        }
        $menu->updated_by = $user?->id;
    }

    public function restoring(Menu $menu): void {
        $user = get_user_data();
        $menu->updated_by = $user?->id;
    }
}
