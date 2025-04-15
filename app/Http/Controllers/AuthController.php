<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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
            'profile' => new UserResource($user),
        ];

        return ApiResponse::success($data, 'Login successful');
    }

    public function logout(Request $request)
    {
        // delete access token
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }

    public function profile(Request $request)
    {
        return ApiResponse::success(new UserResource($request->user()), 'Logged out successfully');
    }
}