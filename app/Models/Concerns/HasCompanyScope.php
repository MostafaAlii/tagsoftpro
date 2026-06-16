<?php

namespace App\Models\Concerns;
trait HasCompanyScope
{
    protected static function bootHasCompanyScope()
    {
        static::addGlobalScope(new \App\Models\Scopes\CompanyScope);
    }
}