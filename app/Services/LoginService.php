<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\LoginServiceInterface;

class LoginService implements LoginServiceInterface
{
    public function login(array $credentials, string $ip): array
    {
        $email = $credentials['email'];
        $throttleKey = Str::lower($email) . '|' . $ip;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in $seconds seconds."]
            ])->status(429);
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.']
            ])->status(401);
        }

        RateLimiter::clear($throttleKey);

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }


    public function logout(): void
    {
        $user = auth()->user();

        if ($user) {
            $user->currentAccessToken()->delete();
        }
    }
}