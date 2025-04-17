<?php

namespace App\Enums;

enum UserRole: int
{
    case Admin = 1;
    case Manager = 2;
    case Employee = 3;

    public function title(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Employee => 'Employee',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Manager => 'Manager',
            self::Employee => 'Employee',
        };
    }
}
