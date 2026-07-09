<?php
namespace App\Services\Theme;
use Illuminate\Support\Facades\{Blade,File};
class ThemeComponentResolver {
    public static function register(): void {
        Blade::componentResolver(function ($class, $data) {
            return null;
        });
    }


    public static function view(string $component): string {
        $theme = ThemeResolver::code();
        $themeView = "dashboard.themes.$theme.components.$component";
        $themePath = resource_path("views/dashboard/themes/$theme/components/" . str_replace('.', '/', $component) .".blade.php");
        if (File::exists($themePath)) {
            return $themeView;
        }
        return "dashboard.themes.default.components.$component";
    }
}