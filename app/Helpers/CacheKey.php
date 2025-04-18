<?php

namespace App\Helpers;

class CacheKey
{
    public static function companyAdmins($companyId): string
    {
        return "company_admins_{$companyId}";
    }
}
