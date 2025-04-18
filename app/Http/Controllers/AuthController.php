<?php

namespace App\Http\Controllers;

use App\Models\User;
// use App\Models\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

public function register(Request $request)
{
    \Log::info('Register start', $request->all());

    try {
        if ($request->user() && $request->user()->role !== 'Admin') {
            return response()->json(['error' => 'Only Admins can register users'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'company_id' => 'required|exists:companies,id',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $validated['company_id'],
            'role' => $validated['role'],
        ]);

        $token = $user->createToken('api')->plainTextToken;

        \Log::info('Register success', ['user_id' => $user->id, 'token' => $token]);

        return response()->json(['user' => $user, 'token' => $token]);
    } catch (\Exception $e) {
        \Log::error('Register error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['user' => $user, 'token' => $user->createToken('api')->plainTextToken]);
    }

    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out successfully']);
}

}
