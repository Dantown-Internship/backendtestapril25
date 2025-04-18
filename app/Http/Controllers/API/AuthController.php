<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterCompanyRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
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

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        if (!Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
        
        $user = User::with('company')->where('email', $validated['email'])->first();
        
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function registerCompany(RegisterCompanyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $company = Company::create([
            'name' => $validated['company_name'],
            'email' => $validated['company_email'],
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);
        
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'message' => 'Company and admin user created successfully',
            'company' => $company,
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('company'),
            'authenticated' => true,
        ]);
    }
}
