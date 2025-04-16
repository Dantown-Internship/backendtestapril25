<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function listUsers(Request $request)
    {
        $user = Auth::user();
        $users = User::where('company_id', $user->company_id)->paginate(10);

        return response()->json($users);
    }

    public function storeUsersData(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = Auth::user();

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $user->company_id,
            'role' => $request->role,
        ]);

        return response()->json($newUser, 201);
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = Auth::user();
        $targetUser = User::where('company_id', $user->company_id)->findOrFail($id);
        $targetUser->update(['role' => $request->role]);

        return response()->json($targetUser);
    }
}