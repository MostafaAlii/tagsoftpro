<?php

namespace App\Enums\MenuItem;

enum MenuItemTarget: string
{
    case SELF = '_self';
    case BLANK = '_blank';

    public function label(): string
    {
        return match ($this) {
            self::SELF => trans('dashboard/menu_items.target_self'),
            self::BLANK => trans('dashboard/menu_items.target_blank'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::SELF->value => trans('dashboard/menu_items.target_self'),
            self::BLANK->value => trans('dashboard/menu_items.target_blank'),
        ];
    }
}