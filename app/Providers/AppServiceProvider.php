<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{View, Cache, Schema};
use App\Models\{AdminPanelSetting};
use Illuminate\Support\Facades\Blade;
use App\Services\Theme\ThemeResolver;
class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        
    }

    public function boot(): void {
        Blade::directive('ownerOnly', function () {
            return '<?php if(\App\Http\Middleware\EnsureOwner::check()): ?>';
});

Blade::directive('endOwnerOnly', function () {
return '<?php endif; ?>';
});
if (Schema::hasTable('admin_panel_settings')) {
$company_code = get_user_data()?->company_id;
$settings = Cache::remember('app_settings_' . ($company_code ?? 'default'), 3600, function () use ($company_code) {
$query = AdminPanelSetting::with('media')->orderBy('created_at', 'DESC');
if ($company_code) {
$query->where('company_id', $company_code);
}
return $query->first();
});
$logo = $settings?->getMediaUrl('setting', $settings, null, 'media', 'logo') ??
asset('dashboard/themes/default/assets/images/default/default.png');
$favicon = $settings?->getMediaUrl('setting', $settings, null, 'media', 'favicon') ??
asset('dashboard/themes/default/assets/images/default/default.png');
$theme_code = ThemeResolver::code();
View::share(compact('settings', 'logo', 'favicon', 'theme_code'));
}
}
}