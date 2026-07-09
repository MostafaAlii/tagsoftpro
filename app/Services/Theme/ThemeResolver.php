<?php
namespace App\Services\Theme;
use App\Models\AdminPanelSetting;
use App\Models\ProjectType;
use Illuminate\Support\Facades\{Cache, File};
class ThemeResolver {
    protected const DEFAULT_THEME = 'default';
    protected const CACHE_TTL = 3600;
    public static function settings(): ?AdminPanelSetting {
        $companyId = get_user_data()?->company_id;
        return Cache::remember(
            static::cacheKey($companyId),static::CACHE_TTL,function () use ($companyId) {
                $query = AdminPanelSetting::with(['media', 'theme'])->orderBy('created_at', 'desc');
                if ($companyId) {
                    $query->where('company_id', $companyId);
                } else {
                    $query->whereNull('company_id');
                }
                return $query->first();
            }
        );
    }
    
    public static function code(?AdminPanelSetting $settings = null): string {
        $settings ??= static::settings();
        $code = $settings?->theme?->code;
        if ($code && static::exists($code)) {
            return $code;
        }
        // default theme
        $projectTypeId = get_user_data()?->company?->project_type_id;
        if ($projectTypeId) {
            $defaultCode = static::defaultCodeForProjectType($projectTypeId);
            if ($defaultCode && static::exists($defaultCode)) {
                return $defaultCode;
            }
        }
        return static::DEFAULT_THEME;
    }

    public static function defaultCodeForProjectType(int $projectTypeId): ?string {
        return Cache::remember(static::projectTypeCacheKey($projectTypeId),static::CACHE_TTL,function () use ($projectTypeId) {
                $projectType = ProjectType::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)
                    ->with(['themes' => function ($q) {
                        $q->wherePivot('is_default', true)
                            ->wherePivot('is_active', true);
                    }])->find($projectTypeId);
                return $projectType?->themes->first()?->code;
            }
        );
    }

    public static function exists(string $code): bool {
        return File::isDirectory(resource_path("views/dashboard/themes/{$code}"))
            && File::isDirectory(public_path("dashboard/themes/{$code}"));
    }

    public static function cacheKey(?int $companyId): string {
        return 'app_settings_' . ($companyId ?? 'default');
    }

    public static function projectTypeCacheKey(int $projectTypeId): string {
        return 'project_type_default_theme_' . $projectTypeId;
    }
    
    public static function forget(?int $companyId = null): void {
        $companyId ??= get_user_data()?->company_id;
        Cache::forget(static::cacheKey($companyId));
    }

    public static function forgetProjectTypeDefault(int $projectTypeId): void {
        Cache::forget(static::projectTypeCacheKey($projectTypeId));
    }
}