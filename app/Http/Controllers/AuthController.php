<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Controller
 *
 * Handles user authentication operations including:
 * - User registration (Admin only)
 * - User login
 * - User logout
 *
 * Uses Laravel Sanctum for token-based authentication.
 */
class AuthController extends Controller
{
    use JsonResponseTrait;

    /**
     * Register a new user in the system.
     *
     * Only accessible by users with Admin role.
     * Creates a new user with the specified role and company.
     * Returns the created user and an authentication token.
     *
     * @param AuthRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRegisterRequest $request)
    {
        $validated = $request->validated();

        // Only allow admin users to create new users
        if (!Auth::user()?->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => Auth::user()->company_id,
            'role' => $validated['role'],
        ]);

        return $this->successResponse([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 'User registered successfully');
    }

    /**
     * Authenticate a user and generate an access token.
     *
     * Validates the user's credentials and returns:
     * - The authenticated user's information
     * - A new authentication token
     *
     * @param AuthLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(AuthLoginRequest $request)
    {
        $validated = $request->validated();

        // First check if user exists
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => ['email' => ['The provided email does not exist.']]
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Then verify password
        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => ['password' => ['The provided password is incorrect.']]
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 'Login successful');
    }

    /**
     * Revoke the current user's authentication token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successMessage('Logged out successfully');
    }
}
