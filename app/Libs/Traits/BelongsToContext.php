<?php

namespace App\Libs\Traits;

use App\Models\Scopes\CompanyScope;


trait BelongsToContext
{
    public static function bootBelongsToContext()
    {
        static::addGlobalScope(new CompanyScope);
    }

}