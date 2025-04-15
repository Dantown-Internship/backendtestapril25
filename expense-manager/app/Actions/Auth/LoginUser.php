<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginUser
{
    public function handle($request)
    {

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            throw new \Exception( 'Invalid credentials provided', 400);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;
        return [
            'token' => $token,
            'user' => $user,
            'company' => $user->company
        ];

    }
}
