<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Register a new user (Admins only).

    public function registerUser(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'company_id' => $validated['company_id'],
            'role'       => $validated['role'],
            'password'   => Hash::make($validated['password']),
        ]);

        return api_response($user, 'User registered successfully.', true, 201);
    }


    // Login user and create access token

    public function loginUser(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return api_response(null, 'Invalid credentials.', false, 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;
        return api_response(['token' => $token], 'Logged in successfully.');
    }


    // Revoke current access token and logout user

    public function logoutUser()
    {
        auth()->user()->currentAccessToken()->delete();
        return api_response(null, 'Logged out successfully.');
    }
}
