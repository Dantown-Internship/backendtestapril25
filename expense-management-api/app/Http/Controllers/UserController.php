<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users,
        ], 200);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8',
    //         'role' => 'required|string|in:Admin,Manager,Employee',
    //         'company_id' => 'required|exists:companies,id',
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //         'role' => $request->role,
    //         'company_id' => $request->company_id,
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User created successfully',
    //         'data' => $user,
    //     ], 201);
    // }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string|in:Admin,Manager,Employee',
            'company_id' => 'sometimes|required|exists:companies,id',
        ]);

        $user->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ], 200);
    }
    

}
