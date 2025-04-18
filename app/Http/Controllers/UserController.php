<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('admin-only');

        $users = User::where('company_id', Auth::User()->company_id)
                   ->select('id', 'name', 'email', 'role')
                   ->paginate(10);

        return response()->json(['message' => 'Fetch successful', 'data' => $users]);
        
    }

    public function store(Request $request)
    {
        $this->authorize('admin-only');

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'company_id' => Auth::user()->company_id,
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    public function updateRole(Request $request, $id)
    {
        $this->authorize('admin-only');

        $user = User::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user->update(['role' => $request->role]);

        return response()->json(['message' => 'User role updated']);
    }
}
