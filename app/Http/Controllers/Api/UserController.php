<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Notifications\VerifyEmailNotification;

class UserController extends Controller
{
    use ApiResponse;

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:Admin,Manager,Employee'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->user()->company_id, // Automatically assign to admin's company
            'role' => $request->role,
        ]);

        // Send verification email
        $user->notify(new VerifyEmailNotification);

        //$token = $user->createToken('auth_token')->plainTextToken;

        return $this->success($user, 'User created successfully and email has been sent for verification.', 201);
    }

    public function getUsers(Request $request)
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->paginate(10);

        return $this->success($users, 'Users retrieved successfully');
    }

    public function updateUser(Request $request, $userId)
    {
        $user = User::where('company_id', $request->user()->company_id)
            ->findOrFail($userId);

        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'role' => ['sometimes', 'in:Admin,Manager,Employee'],
            'password' => ['sometimes', Password::defaults()],
        ]);

        $updateData = $request->only(['name', 'email', 'role']);
        
        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return $this->success($user, 'User updated successfully');
    }
    
} 