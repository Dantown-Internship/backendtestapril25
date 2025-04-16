<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->with(['company:id,name'])
            ->select(['id', 'name', 'email', 'role', 'company_id', 'created_at'])
            ->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Employee'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json($user->load(['company:id,name']), 201);
    }

    public function update(Request $request, User $user)
    {
        // Ensure the user being updated belongs to the same company
        if ($user->company_id !== $request->user()->company_id) {
            abort(403, 'Unauthorized to update this user');
        }

        $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Employee'])],
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return response()->json($user->load(['company:id,name']));
    }
} 