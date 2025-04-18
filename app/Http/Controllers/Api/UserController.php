<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List of all users in the company (Admins only).
     */
    public function getUserList(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)->get();

        return api_response($users, 'Users retrieved successfully.');
    }

    /**
     * Create a new user (Admins only).
     */
    public function createUser(StoreUserRequest $request)
    {
        $attrs = $request->validated();

        $user = User::create([
            'name'       => $attrs['name'],
            'email'      => $attrs['email'],
            'company_id' => $request->user()->company_id,
            'role'       => $attrs['role'],
            'password'   => Hash::make($attrs['password']),
        ]);

        return api_response($user, 'User added successfully.', true, 201);
    }

    /**
     * Update a user’s role (Admins only).
     */
    public function updateUserRole(UpdateUserRoleRequest $request, $id)
    {
        $user = User::where('id', $id)
        ->where('company_id', $request->user()->company_id)
        ->firstOrFail();

        $user->update(['role' => $request->validated()['role']]);

        return api_response($user, 'User role updated successfully.');
    }
}
