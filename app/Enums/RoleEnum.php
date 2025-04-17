<?php


namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case EMPLOYEE = 'Employee';




    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
