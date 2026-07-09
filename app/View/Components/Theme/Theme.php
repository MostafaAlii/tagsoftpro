<?php

namespace App\View\Components\Theme;

use Illuminate\View\Component;
use App\Services\Theme\ThemeComponentResolver;

class Theme extends Component
{
    public function __construct(
        public string $component
    ) {}

    public function render()
    {
        return view(
            ThemeComponentResolver::view($this->component)
        );
    }
}