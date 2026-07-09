<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{View, Schema, Blade};
use App\Services\Theme\{ThemeResolver,ThemeComponentResolver};
use App\View\Components\Theme\Theme;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::component('theme', Theme::class);
        Blade::directive('ownerOnly', function () {
            return '<?php if(\App\Http\Middleware\EnsureOwner::check()): ?>';
});

Blade::directive('endOwnerOnly', function () {
return '<?php endif; ?>';
});

Blade::directive('themeComponent', function ($expression) {
return "<?php echo view(
        \App\Services\Theme\ThemeComponentResolver::view($expression)
    )->render(); ?>";
});
Blade::component(
'theme-page-wrapper',
\App\View\Components\Theme\PageWrapper::class
);
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