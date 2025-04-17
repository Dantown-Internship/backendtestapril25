<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case EMPLOYEE = 'Employee';

    public static function all()
    {
        return [
            self::ADMIN->value,
            self::MANAGER->value,
            self::EMPLOYEE->value,
        ];
    }
}
