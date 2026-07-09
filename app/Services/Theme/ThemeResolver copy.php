<?php

namespace App\Services\Theme;

use App\Models\AdminPanelSetting;
use App\Models\Theme;
use Illuminate\Support\Facades\Cache;

class ThemeResolver
{
    public static function code(): string
    {
        $companyId = get_user_data()?->company_id;

        return Cache::remember(
            'theme_code_' . ($companyId ?? 'owner'),
            3600,
            function () use ($companyId) {

                $settingsQuery = AdminPanelSetting::query();

                if ($companyId) {
                    $settingsQuery->where('company_id', $companyId);
                } else {
                    $settingsQuery->whereNull('company_id');
                }

                $settings = $settingsQuery
                    ->with('theme')
                    ->first();

                /**
                 * 1- الشركة مختارة Theme
                 */
                if ($settings?->theme && (int) $settings->theme->is_active === 1) {
                    return $settings->theme->code;
                }

                /**
                 * 2- Default Theme
                 */
                $defaultTheme = Theme::query()
                    ->where('is_active', 1)
                    ->where('is_default', 1)
                    ->first();

                if ($defaultTheme) {
                    return $defaultTheme->code;
                }

                /**
                 * 3- Hard Fallback
                 */
                return 'default';
            }
        );
    }
}