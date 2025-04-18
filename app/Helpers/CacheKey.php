<?php

namespace App\Helpers;

class CacheKey
{
    public static function companyAdmins($companyUuid): string
    {
        return "company_admins_{$companyUuid}";
    }
}
