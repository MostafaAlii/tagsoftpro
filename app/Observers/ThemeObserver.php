<?php
namespace App\Observers;
use App\Models\Theme;
class ThemeObserver {
    public function creating(Theme $theme): void {
        $user = get_user_data();
        $theme->company_id = $theme->company_id ?? $user?->company_id;
        $theme->created_by = $user?->id;
        /*
         |-----------------------------------------
         | Default Theme Logic (IMPORTANT)
         |-----------------------------------------
         */
        if ($theme->company_id) {
            $hasDefault = Theme::where('company_id', $theme->company_id)->where('is_default', 1)->exists();
            if (!$hasDefault) {
                $theme->is_default = 1;
            }
        }
    }

    public function updating(Theme $theme): void {
        $theme->updated_by = get_user_data()?->id;
    }
}