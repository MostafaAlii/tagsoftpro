<?php

namespace App\Enums\MenuItem;

enum MenuItemLinkType: string
{
    case ROUTE = 'route';
    case URL = 'url';
    case NONE = 'none';

    public function label(): string
    {
        return match ($this) {
            self::ROUTE => trans('dashboard/menu_items.link_type_route'),
            self::URL => trans('dashboard/menu_items.link_type_url'),
            self::NONE => trans('dashboard/menu_items.link_type_none'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::ROUTE->value => trans('dashboard/menu_items.link_type_route'),
            self::URL->value => trans('dashboard/menu_items.link_type_url'),
            self::NONE->value => trans('dashboard/menu_items.link_type_none'),
        ];
    }
}