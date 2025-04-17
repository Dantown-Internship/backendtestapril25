<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface LoginServiceInterface
{
    public function login(array $credentials, string $ip): array;
    public function logout(): void;
}