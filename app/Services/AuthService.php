<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class AuthService
{
   public function __construct()
   {
        // Code here

    }
    public function register($request)
    {
        // Validate the request
        $validated = $request->validated();

        // Create a new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'company_id' => $validated['company_id'],
        ]);

        // Return the created user
        return $user;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->attempt($credentials)) {
            return response()->json(
                ['token' => auth()->user()->createToken('API Token')->plainTextToken
            ]);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
