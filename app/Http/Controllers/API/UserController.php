<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class UserController extends Controller
{
    /**
    * List all users in the authenticated admin's company.
    * Excludes the currently authenticated admin from the result.
    */
    public function index(Request $request)
    {
        $admin = $request->user();
        $companyId = $admin->company_id;

        // Unique cache key for listing users in the company
        $cacheKey = "users_company_{$companyId}";

        // Cache result for 5 minutes (300 seconds)
        $users = Cache::remember($cacheKey, 300, function () use ($admin) {
            return User::where('company_id', $admin->company_id)
                        ->where('id', '!=', $admin->id)
                        ->get();
        });

        return response()->json([
            'users' => $users
        ]);
    }

    /**
    * Create a user role (Admin only).
    */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Clear cache for user listings
        Cache::flush();


        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'company_id' => $user->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->string('password')),
        ]);


        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }


    /**
    * Update a user's role (Admin only).
    */

    public function updateRole(Request $request, User $user)
    {
        // Clear cache for user listings
        Cache::flush();

        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully.'
        ]);
    }

}
