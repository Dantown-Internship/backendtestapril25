<?php

namespace App\Traits;

trait HasRole
{
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'Manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'Employee';
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canManageExpenses(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canViewExpenses(): bool
    {
        return true; // All roles can view expenses
    }

    public function canCreateExpenses(): bool
    {
        return true; // All roles can create expenses
    }
} 