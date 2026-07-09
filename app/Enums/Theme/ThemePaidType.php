<?php

namespace App\Enums\Theme;

enum ThemePaidType: string
{
    case FREE = 'free';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::FREE => trans('dashboard/themes.paid_type_free'),
            self::PAID => trans('dashboard/themes.paid_type_paid'),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::FREE => '<span class="badge bg-success">' . $this->label() . '</span>',
            self::PAID => '<span class="badge bg-warning">' . $this->label() . '</span>',
        };
    }
}