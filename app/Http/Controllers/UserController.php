<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/users (Admin only)
    public function index()
    {
        return User::where('company_id', auth()->user()->company_id)->get();
    }

    // POST /api/users (Admin only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => auth()->user()->company_id,
            'role' => $validated['role'],
        ]);

        return response()->json(['message' => 'User created', 'data' => $user], 201);
    }

    // PUT /api/users/{id} (Admin only)
    public function update(Request $request, $id)
    {
        $user = User::where('company_id', auth()->user()->company_id)->findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user->update(['role' => $validated['role']]);

        return response()->json(['message' => 'User role updated', 'data' => $user]);
    }
}
