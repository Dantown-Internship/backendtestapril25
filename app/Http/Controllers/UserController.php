<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Central\Tenant;
use App\Services\TenantService;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant\User as TenantUser;
use App\Models\Central\User as CentralUser;

class UserController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
        $this->middleware('auth:sanctum');
    }

    /**
     * List all users in the tenant's database.
     */
    public function index(Request $request)
    {
        $tenantUser = TenantUser::where('central_user_id', $request->user()->id)->firstOrFail();

        // Only Admin or Manager can list users
        if (!$tenantUser->isAdmin() && !$tenantUser->isManager()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $users = TenantUser::where('role', '!=', 'Admin')->get();

        return response()->json(['users' => $users], 200);
    }

    /**
     * Create a new user in the tenant's database.
     */
    public function store(Request $request)
    {
        $tenantUser = TenantUser::where('central_user_id', $request->user()->id)->firstOrFail();

        // Only Admin can create users
        // if (!$tenantUser->isAdmin()) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:Admin,Manager,Employee',
            'password' => 'sometimes|string|min:8|confirmed', // Optional password
        ]);

        // Check if email is already used in this tenant
        if (TenantUser::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email already registered for this tenant'], 422);
        }

        // Use the authenticated Admin's central_user_id
        $centralUserId = $request->user()->id;

        // Create tenant-specific user
        $tenantUser = TenantUser::create([
            'central_user_id' => $centralUserId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => $request->role,
        ]);

        return response()->json([
            'user' => $tenantUser,
        ], 201);
    }

    /**
     * Update an existing user in the tenant's database.
     */
    public function update(Request $request, TenantUser $user)
    {
        $tenantUser = TenantUser::where('central_user_id', $request->user()->id)->firstOrFail();

        // Only Admin can update users
        if (!$tenantUser->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            // 'role' => 'sometimes|in:Admin,Manager,Employee',
            // 'password' => 'sometimes|string|min:8|confirmed', // Optional password
        ]);

        // Update tenant-specific user
        $user->update($request->only(['name', 'email', 'role']));

        return response()->json(['user' => $user], 200);
    }

    /**
     * Delete a user from the tenant's database.
     */
    public function destroy(Request $request, TenantUser $user)
    {
        $tenantUser = TenantUser::where('central_user_id', $request->user()->id)->firstOrFail();

        // Only Admin can delete users
        if (!$tenantUser->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent deleting the last Admin
        if ($user->role === 'Admin' && TenantUser::where('role', 'Admin')->count() === 1) {
            return response()->json(['error' => 'Cannot delete the last Admin'], 422);
        }

        // Delete tenant-specific user
        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }
}