<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = "Admin";

    case EMPLOYEE = "Employee";

    case MANAGER = "Manager";

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a user can manage expenses.
     *
     * @return bool
     */
    public function canManageExpenses(): bool
    {
        return match($this) {
            self::ADMIN, self::MANAGER => true,
            self::EMPLOYEE => false,
        };
    }

    /**
     * Check if a user can manage users.
     *
     * @return bool
     */
    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }

}
