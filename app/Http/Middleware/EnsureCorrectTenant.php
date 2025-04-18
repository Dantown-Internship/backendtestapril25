<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\Central\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:Admin');
        $this->middleware('tenant');
    }

    public function index(Request $request)
    {
        $users = User::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        // Create user in central database, scoped to the Admin's tenant
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $request->user()->tenant_id,
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $user)
    {
        // Ensure the user belongs to the same tenant
        if ($user->tenant_id != $request->user()->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user->update(['role' => $request->role]);

        return response()->json($user);
    }
}