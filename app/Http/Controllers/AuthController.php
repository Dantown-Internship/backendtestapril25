<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'company_name' => 'required|string|unique:companies,name',
                'role' => 'required|in:Admin',
            ]);

            // Only create company for Admin users
            if ($validated['role'] === 'Admin') {
                $company = Company::create([
                    'name' => $validated['company_name'],
                    'email' => $validated['email'],
                ]);

                if (!$company) {
                    return response()->json(['message' => 'Failed to create company'], 500);
                } else {
                    // Create user
                    User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => bcrypt($validated['password']),
                        'company_id' => $company->id,
                        'role' => $validated['role']
                    ]);
                }
            }

            return response()->json(['message' => "Registered successfully"], 201);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'The email address is already taken. Please choose a different one.'], 400);
            }

            // Catch other query exceptions
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function register_user(Request $request)
    {
        // Ensure the authenticated user is Admin
        if (!$request->user() || $request->user()->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        // Get the company from the current admin
        $company = $request->user()->company;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'company_id' => $company->id,
        ]);

        return response()->json([
            'message' => 'User registered successfully.',
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        return response()->json(['token' => $user->createToken('auth_token')->plainTextToken]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
