<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeRole(['Admin']);

        $users = User::where('company_id', auth()->user()->company_id)->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => auth()->user()->company_id, // Only admins can create users in their own company
        ]);

        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeRole(['Admin']);

        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $user->update(['role' => $request->role]);

        return response()->json(['message' => 'User role updated']);
    }
}
