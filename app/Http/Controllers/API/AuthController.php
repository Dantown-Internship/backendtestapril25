<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register a new user (Admin only).
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Get validated data
        $validated = $request->validated();
        
        // Create user with the admin's company_id
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $request->user()->company_id,
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Login a user and return a Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Get validated data
        $validated = $request->validated();
        
        // Verify credentials
        if (!Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        
        $user = User::with('company')->where('email', $validated['email'])->first();
        
        // Create a token
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Register a new company and admin user (for first-time setup).
     */
    public function registerCompany(Request $request): JsonResponse
    {
        $request->validate([
            'company_name' => 'required|string|unique:companies,name|max:255',
            'company_email' => 'required|email|unique:companies,email|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Create company
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
        ]);
        
        // Create admin user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);
        
        // Create token
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'message' => 'Company and admin user created successfully',
            'company' => $company,
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
