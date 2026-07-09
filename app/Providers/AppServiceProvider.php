<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{View, Schema};
use Illuminate\Support\Facades\Blade;
use App\Services\Theme\ThemeResolver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('ownerOnly', function () {
            return '<?php if(\App\Http\Middleware\EnsureOwner::check()): ?>';
});

Blade::directive('endOwnerOnly', function () {
return '<?php endif; ?>';
});

if (Schema::hasTable('admin_panel_settings')) {
View::composer('*', function ($view) {
$settings = ThemeResolver::settings();

$logo = $settings?->getMediaUrl('setting', $settings, null, 'media', 'logo')
?? asset('dashboard/themes/default/assets/images/default/default.png');

$favicon = $settings?->getMediaUrl('setting', $settings, null, 'media', 'favicon')
?? asset('dashboard/themes/default/assets/images/default/default.png');

$theme_code = ThemeResolver::code($settings);

$view->with(compact('settings', 'logo', 'favicon', 'theme_code'));
});
}
}
}