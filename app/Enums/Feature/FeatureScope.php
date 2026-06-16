<?php
namespace App\Enums\Feature;
enum FeatureScope: string {
    case MAIN  = 'main';
    case ADDON = 'addon';
    public function label(): string {
        return match ($this) {
            self::MAIN  => trans('dashboard/features.scope_main'),
            self::ADDON => trans('dashboard/features.scope_addon'),
        };
    }

    public function badge(): string {
        return match ($this) {
            self::MAIN  => '<span class="badge bg-secondary">' . $this->label() . '</span>',
            self::ADDON => '<span class="badge bg-warning">'    . $this->label() . '</span>',
        };
    }
}