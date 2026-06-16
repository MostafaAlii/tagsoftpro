<?php

namespace App\Enums\Theme;

enum ThemeDefault: int
{
    case YES = 1;
    case NO  = 0;

    public function label(): string
    {
        return match ($this) {
            self::YES => 'Default',
            self::NO  => 'Normal',
        };
    }
}