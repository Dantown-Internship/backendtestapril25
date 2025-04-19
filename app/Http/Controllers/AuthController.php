<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Admin-only registration endpoint
    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|unique:users',
            'password'   => ['required', 'confirmed', Password::min(6)],
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => $request->password, 
            'company_id' => $request->company_id,
            'role'       => $request->role,
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }
    
    

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // $user = User::where('email', $request->email)->first();
    //     $user = User::first(); // just to test if a user is fetched


    //     \Log::info('User:', ['user' => $user]);
    //     \Log::info('Password Match:', [
    //         'input_password' => $request->password,
    //         'stored_password' => optional($user)->password,
    //         'match' => $user ? Hash::check($request->password, $user->password) : null,
    //     ]);
        
    


    //     if (! $user || ! Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     $token = $user->createToken('api-token')->plainTextToken;

    //     return response()->json([
    //         'token' => $token,
    //         'user'  => $user
    //     ]);
    // }

    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    \Log::info('Login attempt for email: ' . $request->email);
    \Log::info('User found:', ['user' => $user]);

    if (! $user || ! Hash::check($request->password, $user->password)) {
        \Log::info('Password check result:', [
            'input_password' => $request->password,
            'stored_password' => optional($user)->password,
            'match' => $user ? Hash::check($request->password, $user->password) : null,
        ]);

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type'   => 'Bearer',
    ]);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    
}
