<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    public function createUser(array $data): User;
    public function updateUser(array $data, User $user): bool;
}
