<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();

        //  Allows only Admin
        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // 📦 Fetch all users in same company
        $users = User::where('company_id', $user->company_id)->get();

        return response()->json($users);
    }



    public function store(Request $request)
    {
        $admin = auth()->user();

        if ($admin->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['Manager', 'Employee'])],
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'company_id' => $admin->company_id,
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user
        ], 201);
    }

    

    public function update(Request $request, $id)
    {
        $admin = auth()->user();

        if ($admin->role !== 'Admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Employee'])]
        ]);

        $user = \App\Models\User::where('company_id', $admin->company_id)->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found or not in your company'], 404);
        }

        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => 'User role updated successfully.',
            'user' => $user
        ]);
    }



}

