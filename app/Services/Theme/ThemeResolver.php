<?php
namespace App\Services\Theme;
use App\Models\{AdminPanelSetting,Theme};
use Illuminate\Support\Facades\Cache;
class ThemeResolver {
    public static function code(): string {
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
                $settings = $settingsQuery->with('theme')->first();
                // 1. from settings
                if ($settings?->theme?->code) {
                    return $settings->theme->code;
                }
                // 2. default theme from DB
                $defaultTheme = Theme::where('is_default', 1)->first();
                if ($defaultTheme?->code) {
                    return $defaultTheme->code;
                }
                // 3. hard fallback
                return 'default';
            }
        );
    }
}