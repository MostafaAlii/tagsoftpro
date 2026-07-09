<?php

namespace App\View\Components\Theme;

use Illuminate\View\Component;
use App\Services\Theme\ThemeComponentResolver;

class PageWrapper extends Component
{
    public function render()
    {
        return view(
            ThemeComponentResolver::view('layouts.page-wrapper')
        );
    }
}