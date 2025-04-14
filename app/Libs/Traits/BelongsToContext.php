<?php

namespace App\Libs\Traits;

use App\Models\Scopes\CompanyScope;


trait BelongsToContext
{
    public function bootedBelongsToContext()
    {
        static::addGlobalScope(new CompanyScope);
    }

}