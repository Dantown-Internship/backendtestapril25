<?php

namespace App\Http\Controllers\API;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users in the authenticated user's company.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = $request->query('limit', 10); // Default to 10 items per page
        $limit = min(max((int)$limit, 1), 100); // Ensure limit is between 1 and 100

        $users = User::where('company_id', $user->company_id)
            ->paginate($limit, ['*'], 'page', $request->query('page'))
            ->toResourceCollection();

        return response()->json($users);
    }

    /**
     * Store a newly created user in the company.
     */
    public function store(Request $request)
    {
        $currentUser = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(UserRole::toArray())],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $currentUser->company_id,
        ]);

        return response()->json([
            'message' => 'User added successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, $id)
    {
        $currentUser = $request->user();

        // Users can view their own profile, admins and managers can view any user in their company
        $user = User::where('id', $id)
            ->where('company_id', $currentUser->company_id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->id !== $currentUser->id && !$currentUser->isAdmin() && !$currentUser->isManager()) {
            return response()->json(['message' => 'Unauthorized to view this user'], 403);
        }

        return response()->json(['message' => 'User retrieved successfully!', 'data' => $user]);
    }

    /**
     * Update the specified user's role.
     */
    public function update(Request $request, $id)
    {
        $currentUser = $request->user();

        // Find the user to update
        $user = User::where('id', $id)
            ->where('company_id', $currentUser->company_id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate the role
        $request->validate([
            'role' => ['required', Rule::in(UserRole::toArray())],
        ]);

        // Update the role
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, $id)
    {
        $currentUser = $request->user();

        // Admin cannot delete themselves
        if ($currentUser->id == $id) {
            return response()->json(['message' => 'Cannot delete your own account'], 400);
        }

        $user = User::where('id', $id)
            ->where('company_id', $currentUser->company_id)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
