<?php

namespace Modules\Provider\Enums;

enum ProviderStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';

    public function badge(): string
    {
        $color = match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::PENDING => 'warning',
            self::SUSPENDED => 'secondary',
        };

        return '<span class="badge bg-' . $color . '">'
            . trans('provider::providers.status_' . $this->value)
            . '</span>';
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::ACTIVE->value => trans('provider::providers.status_active'),
            self::INACTIVE->value => trans('provider::providers.status_inactive'),
            self::PENDING->value => trans('provider::providers.status_pending'),
            self::SUSPENDED->value => trans('provider::providers.status_suspended'),
        ];
    }
}