<?php
namespace App\Enums\Feature;
enum FeatureType: string {
    case UI        = 'ui';
    case ORDERING  = 'ordering';
    case ANALYTICS = 'analytics';
    public function label(): string {
        return match ($this) {
            self::UI        => trans('dashboard/features.type_ui'),
            self::ORDERING  => trans('dashboard/features.type_ordering'),
            self::ANALYTICS => trans('dashboard/features.type_analytics'),
        };
    }

    public function badge(): string {
        return match ($this) {
            self::UI        => '<span class="badge bg-info">'    . $this->label() . '</span>',
            self::ORDERING  => '<span class="badge bg-primary">' . $this->label() . '</span>',
            self::ANALYTICS => '<span class="badge bg-warning">' . $this->label() . '</span>',
        };
    }
}