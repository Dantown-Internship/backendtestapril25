<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 200);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Admin,Manager,Employee',
            'company_id' => 'required|exists:companies,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $validatedData = $validator->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'company_id' => $validatedData['company_id'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    public function logout(Request $request) {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ], 200);
    }
    
}
