<?php

namespace App\Http\Controllers\Api;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Check if user is an admin
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::where('company_id', $user->company_id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ]);
    }


    public function store(Request $request)
    {
        $user = $request->user();

        // Check if user is an admin
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(Roles::class)],
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($request->password),
            'company_id' => $user->company_id, // Same company as the admin
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $newUser,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $currentUser = $request->user();

        // Check if user is an admin
        if (!$currentUser->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(Roles::class)],
        ]);

        // Find the user within the current company
        $user = User::where('id', $id)
            ->where('company_id', $currentUser->company_id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Keep old values for audit log
        $oldValues = $user->toArray();

        // Update user role
        $user->update([
            'role' => $request->role,
        ]);

        // Create audit log
        AuditLog::create([
            'user_id' => $currentUser->id,
            'company_id' => $currentUser->company_id,
            'action' => 'user_role_updated',
            'changes' => [
                'old' => $oldValues,
                'new' => $user->toArray(),
            ],
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $user->fresh(),
        ]);
    }
}
