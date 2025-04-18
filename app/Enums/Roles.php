<?php
namespace App\Enums;

enum Roles: string
{
    case ADMIN = "Administrator";
    case MANAGER = "Manager";
    case EMPLOYEE = "Employee";

    public static function values()
    {
        return array_column(Roles::cases(), 'value');
    }
}