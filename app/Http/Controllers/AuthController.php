<?php

namespace App\Http\Controllers;

use App\Enum\RoleEnum;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();

        $user = Auth::user();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => strtolower($validatedData['email']),
            'company_id' => $user->company_id,
            'password' => Hash::make($validatedData['password']),
            'role' => RoleEnum::tryFrom($validatedData['role'])?->value
        ]);

        if(!$user)
        {
            return response()->json([
                'message' => 'unable to register user',
            ], 400);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function listUsers(): \Illuminate\Http\JsonResponse
    {
        $auth_user = Auth::user();

        $cacheKey = 'users_' . $auth_user->company_id;

        if (Cache::has($cacheKey)) {
            $users = Cache::get($cacheKey);
        } else {
            $users = User::with(['company', 'expenses'])
                ->where('company_id', $auth_user->company_id)
                ->get();

            Cache::put($cacheKey, $users, now()->addMinutes(10));
        }
//        $users = User::with(['company', 'expenses'])->where('company_id', $auth_user->company_id)->get();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'users' => $users
        ], 200);
    }
    public function updateUserRole(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee'
        ]);

        $auth_user = Auth::user();

        $user = User::query()->where('id', $id)->where('company_id', $auth_user->company_id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update([
            'role' => RoleEnum::tryFrom($request->role)?->value
        ]);

        return response()->json([
            'message' => 'User role updated successfully',
            'user' => $user
        ], 200);
    }
    public function deleteUser($id): \Illuminate\Http\JsonResponse
    {
        $auth_user = Auth::user();

        $user = User::query()->where('id', $id)->where('company_id', $auth_user->company_id)->first();

        if(!$user)
        {
            return response()->json([
                'message' => 'User not found!'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted!'
        ], 200);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required',
                'password' => 'required'
            ]);
        }catch (ValidationException $e)
        {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        // Normalize email to lowercase
        $email = strtolower($request->input('email'));

        // Find user with case-insensitive email match
        $user = User::where('email', 'ILIKE', $email)->first();

        // Verify credentials
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        Auth::login($user);
//        accessToken
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ], 200);



    }
}
