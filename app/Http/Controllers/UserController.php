<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiHelpers;
    //
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
        ]);

        if (!$this->isAdmin(auth()->user())) {
            return $this->onError(403, 'You are not authorized to create a user');
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->company_id = $request->company_id;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function getUsers(Request $request)
    {
        if (!$this->isAdmin(auth()->user())) {
            return $this->onError(403, 'You are not authorized to view users');
        }

        $users = User::with('company')->get();

        return $this->onSuccess($users, 'Users retrieved successfully');
    }
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'role' => 'sometimes|in:Admin,Manager,Employee',

        ]);

        if (!$this->isAdmin(auth()->user())) {
            return $this->onError(403, 'You are not authorized to update a user');
        }

        $user = User::find($id);

        if (!$user) {
            return $this->onError(404, 'User not found');
        }

        $user->update($request->all());

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
}
