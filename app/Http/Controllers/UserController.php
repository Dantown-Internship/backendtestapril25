<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\Roles;

class UserController extends Controller
{
    /**
     * Display a listing of the users for the authenticated user's company.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $users = User::where('company_id', $companyId)
            ->paginate(10); // Adjust pagination as needed

        return response()->json($users);
    }


    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:' . implode(',', Roles::values()), // Use enum values
        ]);

        $user = User::create([
            'company_id' => $request->user()->company_id, // Assign to the admin's company
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return response()->json($user, 201);
    }


    /**
     * Update the role of the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Ensure the user belongs to the admin's company (multi-tenancy check)
        if ($user->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized to update this user'], 403);
        }

        $validatedData = $request->validate([
            'role' => 'required|in:' . implode(',', Roles::values()),
        ]);

        $user->update(['role' => $validatedData['role']]);

        return response()->json($user);
    }
    // ... store and update methods will be added later
}