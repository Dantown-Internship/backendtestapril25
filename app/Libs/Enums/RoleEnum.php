<?php

namespace App\Libs\Enums;

enum RoleEnum: string
{
    case ADMIN =  'admin';
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';

    /**
     * Return array of the enum values
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}