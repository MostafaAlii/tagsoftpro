<?php
namespace App\Services\Theme;
use Illuminate\Support\Facades\File;
class ThemeIconResolver {
    // ─── الكلاس الأساسي المطلوب مع كل ملف (fontawesome بيتحدد ديناميك) ───
    protected static array $baseClassMap = [
        'tabler-icons.min.css' => 'ti',       // محتاج .ti زيادة
        'feather.css'          => 'feather',  // محتاج .feather زيادة
        'material.css'         => null,       // ligature-based، متجاهلينه
    ];

    public static function all(?string $theme = null): array
    {
        $theme ??= ThemeResolver::code();

        return cache()->remember("theme_icons_{$theme}", 3600, function () use ($theme) {

            $fontPath = public_path("dashboard/themes/{$theme}/assets/fonts");

            if (!File::isDirectory($fontPath)) {
                return [];
            }

            $icons = [];

            foreach (File::glob($fontPath . '/*.css') as $file) {
                $filename = basename($file);
                $css = File::get($file);

                // ─── معاملة خاصة لملف Font Awesome ─────────────────
                if ($filename === 'fontawesome.css') {
                    $icons = array_merge($icons, static::parseFontAwesome($css));
                    continue;
                }

                // ─── باقي الملفات (tabler, feather) ─────────────────
                if (!array_key_exists($filename, static::$baseClassMap)) {
                    continue;
                }

                $base = static::$baseClassMap[$filename];

                if ($base === null) {
                    continue;
                }

                preg_match_all('/\.([a-zA-Z0-9_-]+)::?before/', $css, $matches);

                foreach (array_unique($matches[1] ?? []) as $iconClass) {
                    $fullClass = $base !== '' ? "{$base} {$iconClass}" : $iconClass;
                    $icons[$iconClass] = $fullClass;
                }
            }

            ksort($icons);

            return $icons;
        });
    }

    /**
     * يحلل ملف Font Awesome ويحدد لكل أيقونة العائلة الصحيحة
     * (fab للبراندات، far للـ regular، fas للـ solid)
     */
    protected static function parseFontAwesome(string $css): array
    {
        $styleMap = [];

        preg_match_all('/([^{}]+)\{([^}]*)\}/s', $css, $blocks, PREG_SET_ORDER);

        foreach ($blocks as $block) {
            $selectors = $block[1];
            $body = $block[2];

            $isBrands  = stripos($body, 'Brands') !== false;
            $isSolid   = preg_match('/font-weight\s*:\s*900/', $body);
            $isRegular = preg_match('/font-weight\s*:\s*400/', $body) && stripos($body, 'font-family') !== false;

            if ($isBrands) {
                $style = 'fab';
            } elseif ($isSolid) {
                $style = 'fas';
            } elseif ($isRegular) {
                $style = 'far';
            } else {
                continue;
            }

            preg_match_all('/\.(fa-[a-zA-Z0-9_-]+)/', $selectors, $iconMatches);

            foreach ($iconMatches[1] as $iconClass) {
                $styleMap[$iconClass] ??= $style;
            }
        }

        preg_match_all('/\.(fa-[a-zA-Z0-9_-]+)::?before/', $css, $matches);

        $icons = [];

        foreach (array_unique($matches[1] ?? []) as $iconClass) {
            $style = $styleMap[$iconClass] ?? 'fas';
            $icons[$iconClass] = "{$style} {$iconClass}";
        }

        return $icons;
    }

    public static function cssUrls(?string $theme = null): array
    {
        $theme ??= ThemeResolver::code();

        return cache()->remember("theme_icon_css_{$theme}", 3600, function () use ($theme) {

            $fontPath = public_path("dashboard/themes/{$theme}/assets/fonts");

            if (!File::isDirectory($fontPath)) {
                return [];
            }

            $cssFiles = File::glob($fontPath . '/*.css');

            return array_map(function ($file) use ($theme) {
                return asset("dashboard/themes/{$theme}/assets/fonts/" . basename($file));
            }, $cssFiles);
        });
    }

    public static function forget(string $theme): void
    {
        cache()->forget("theme_icons_{$theme}");
        cache()->forget("theme_icon_css_{$theme}");
    }
}