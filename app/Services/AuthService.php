<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService implements AuthServiceInterface
{
    public function register(array $validatedData): array
    {
        $company = Company::create($validatedData['company']);

        unset($validatedData['company']);

        $user = User::create([
            ...$validatedData,
            'company_id' => $company->id,
            'role'       => RoleEnum::ADMIN(),
            'password'   => Hash::make($validatedData['password']),
        ]);

        return [
            'user'  => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ['user' => null, 'token' => null];
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')
            ->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();
        $token->delete();
    }
}
