<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{User,Company, Expense};

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:Admin,Manager,Employee',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'company_id' => $request->user()->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::where('company_id', $request->user()->company_id)
                    ->findOrFail($id);

        $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Manager', 'Employee'])]
        ]);

        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'User role updated', 'user' => $user]);
    }

}
