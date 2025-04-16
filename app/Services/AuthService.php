<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthService
{

    use HttpResponses;
    public function __construct()
    {
        //
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $request->company_id,
            'role'       => $request->role,
        ]);

        return $this->success([
            'user' => new UserResource($user)
        ], 'User created successfully');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->error('Invalid credentials', 500);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Incorrect Password'], 401);
        }

        $user = Auth::user();

        //Delete all existing tokens for the user (force logout from other devices)
        $user->tokens()->delete();

        $token = $user->createToken($request->email)->plainTextToken;

        return $this->success([
            [
                'user' => $user,
                'access_token' => $token,

            ]
        ], 'Logged in successfully');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->success(null, 'Logged out successfully');
    }
}
