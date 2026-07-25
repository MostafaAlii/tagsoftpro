<?php

namespace App\Enums\MenuItem;

enum MenuItemType: string
{
    case LINK = 'link';
    case DROPDOWN = 'dropdown';
    case HEADER = 'header';
    case DIVIDER = 'divider';

    public function label(): string
    {
        return match ($this) {
            self::LINK => trans('dashboard/menu_items.type_link'),
            self::DROPDOWN => trans('dashboard/menu_items.type_dropdown'),
            self::HEADER => trans('dashboard/menu_items.type_header'),
            self::DIVIDER => trans('dashboard/menu_items.type_divider'),
        };
    }

    public function badge(): string
    {
        $colors = [
            'link' => 'primary',
            'dropdown' => 'warning',
            'header' => 'info',
            'divider' => 'secondary',
        ];

        return '<span class="badge bg-' . $colors[$this->value] . '">' . $this->label() . '</span>';
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::LINK->value => trans('dashboard/menu_items.type_link'),
            self::DROPDOWN->value => trans('dashboard/menu_items.type_dropdown'),
            self::HEADER->value => trans('dashboard/menu_items.type_header'),
            self::DIVIDER->value => trans('dashboard/menu_items.type_divider'),
        ];
    }
}
