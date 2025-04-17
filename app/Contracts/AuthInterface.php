<?php

namespace App\Contracts;
use App\Models\User;

interface AuthInterface
{
    public function signup(array $data, string $roleName): User;
    public function signin(array $credentials): array;
    public function logout(): bool;
    public function me(): User;
}
