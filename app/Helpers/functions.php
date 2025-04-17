<?php

use App\Enums\Role;

if (!function_exists('roleMiddleware'))
{
    function roleMiddleware(...$roles) {
        return 'role:' . implode(',', array_map(fn($role) => $role instanceof Role ? $role->value : $role, $roles));
    }
}
