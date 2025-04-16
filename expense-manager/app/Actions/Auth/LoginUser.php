<?php

namespace App\Actions\Auth;

use Exception;
use Illuminate\Support\Facades\Auth;

class LoginUser
{
    public function handle($request): array
    {

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            throw new Exception('Invalid credentials provided', 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'company' => $user->company,
        ];

    }
}
