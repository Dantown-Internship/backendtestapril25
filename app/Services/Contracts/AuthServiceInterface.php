<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    /**
     * @return array{user:User, token:string}
     */
    public function register(array $validated): array;

    /**
     * @return array{user:User|null, token:string|null}
     */
    public function login(string $email, string $password): array;

    /** Revoke the user’s current token. */
    public function logout(User $user): void;

    /** Create a new user for the admin's company */
    public function createUser(User $creator, array $data): User;
}
