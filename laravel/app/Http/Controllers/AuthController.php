<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\Services\AuthService;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $company = $this->authService->create([
            'name' => $validated['company_name'],
            'email' => $validated['company_email'],
        ]);

        $user = $this->authService->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);

        return response()->json([
            'message' => 'Admin registered successfully.',
            'token' => $user->createToken('API Token')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $this->authService->authenticate($validated['email'], $validated['password']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return response()->json([
            'message' => 'Logged in successfully.',
            'token' => $user->createToken('API Token')->plainTextToken,
        ], 200);
    }
}
