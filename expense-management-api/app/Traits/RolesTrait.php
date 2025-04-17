<?php

namespace App\Traits;

use App\Enums\Roles;

trait RolesTrait
{

    public function isAdmin(): bool
    {
        return $this->role === Roles::ADMIN->value;
    }

    public function isManager(): bool
    {
        return $this->role === Roles::MANAGER->value;
    }

    public function isEmployee(): bool
    {
        return $this->role === Roles::EMPLOYEE->value;
    }
}
