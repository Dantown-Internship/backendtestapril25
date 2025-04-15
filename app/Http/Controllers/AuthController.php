<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;


        $data = [
            'token'   => $token,
            'profile' => $user,
        ];

        return ApiResponse::success($data, 'Login successful');
    }
}
