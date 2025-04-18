<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case EMPLOYEE = 'Employee';

    /**
     * Get all available roles as an array
     *
     * @return array<string>
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all available roles as a formatted array for validation rules
     *
     * @return string
     */
    public static function validationString(): string
    {
        return implode(',', self::toArray());
    }
}
