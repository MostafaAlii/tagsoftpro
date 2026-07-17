<?php
namespace App\Enums\Menu;
enum MenuStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    public function badge(): string {
        return match ($this) {
            self::ACTIVE => '<span class="badge bg-success">' . trans('dashboard/menus.status_active') . '</span>',
            self::INACTIVE => '<span class="badge bg-danger">' . trans('dashboard/menus.status_inactive') . '</span>',
        };
    }

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array {
        return [
            self::ACTIVE->value => trans('dashboard/menus.status_active'),
            self::INACTIVE->value => trans('dashboard/menus.status_inactive'),
        ];
    }
}
