<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
   /**
     * Display a listing of the users.
     * * Admin:returns all users in their company.
     * * Others: returns only the user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser->role === 'Admin') {
            $users = User::where('company_id', $loggedInUser->company_id)->get();
        } else {
            $users = User::where('id', $loggedInUser->id)->get();
        }

        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     * Only Admin can use this.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => Auth::user()->company_id,
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Update the specified user in storage.
     * Only Admin can use this.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        // Ensure that the user being updated is in the same company.
        if ($user->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validationRules = [
            'name' => 'string|max:255',
            'email' => 'string|email|unique:users,email,' . $user->id,
            'role' => 'in:Admin,Manager,Employee',
        ];

        if ($request->filled('password')) {
            $validationRules['password'] = 'string|min:8';
        }

        $validator = Validator::make($request->all(), $validationRules);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $request->except('password');
        if($request->filled('password')){
            $updateData['password'] = Hash::make($request->password);
        }
        $user->update($updateData);

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    /**
     * Remove the specified user from storage.
     * Only Admin can use this.
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        // Ensure that the user being deleted is in the same company.
        if ($user->company_id !== Auth::user()->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
