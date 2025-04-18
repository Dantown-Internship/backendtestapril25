<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(['users' => User::all()]);
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'company_id' => 'required|exists:companies,id',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $validated['company_id'],
            'role' => $validated['role'],
        ]);

        return response()->json(['user' => $user], 201);
    }



    protected function authorizeCompany(User $user, $authUser)
{

    // \Log::info('Company authorization check', [
    //     'auth_user_company' => $authUser->company_id,
    //     'target_user_company' => $user->company_id,
    // ]);

    // if ($user->company_id !== $authUser->company_id) {
    //     abort(403, 'Unauthorized'); 
    // }
}


public function destroy($id, Request $request)
{
    $user = User::find($id);

    if (!$user) {
        Log::warning('User not found for deletion', ['user_id' => $id]);
        return response()->json(['error' => 'User not found'], 404);
    }

    Log::info('Delete user start', ['user_id' => $user->id]);

    try {
        $authUser = $request->user();

        if ($authUser->id === $user->id) {
            return response()->json(['error' => 'You cannot delete your own account'], 403);
        }

        $this->authorizeCompany($user, $authUser);

        $userData = $user->only(['id', 'name', 'email', 'company_id', 'role']);

        $user->delete();

        \App\Models\AuditLog::create([
            'user_id' => $authUser->id,
            'action' => 'delete',
            'model_type' => 'User',
            'model_id' => $user->id,
            'company_id' => $user->company_id,
            'changes' => json_encode($userData),
            'performed_at' => now(),
        ]);

        Log::info('Delete user success', ['user_id' => $user->id]);

        return response()->json(['message' => 'User deleted successfully']);
    } catch (\Exception $e) {
        Log::error('Delete user error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



// public function destroy(User $user, Request $request)
    // {
    //     Log::info('Delete user start', ['user_id' => $user->id]);

    //     try {
    //         $authUser = $request->user();

    //         // Prevent self-deletion
    //         if ($authUser->id === $user->id) {
    //             return response()->json(['error' => 'You cannot delete your own account'], 403);
    //         }

    //         $this->authorizeCompany($user, $authUser);

    //         // Capture user data for audit log
    //         $userData = $user->only(['id', 'name', 'email', 'company_id', 'role']);
    //         if (!$user) {
    //             Log::warning('User not found for deletion');
    //             return response()->json(['error' => 'User not found'], 404);
    //         }
    //         $user->delete();

    //         \App\Models\AuditLog::create([
    //             'user_id' => $authUser->id,
    //             'action' => 'delete',
    //             'model_type' => 'User',
    //             'model_id' => $user->id,
    //             'company_id' => $user->company_id,
    //             'changes' => json_encode($userData),
    //             'performed_at' => now(),
    //         ]);

    //         Log::info('Delete user success', ['user_id' => $user->id]);

    //         return response()->json(['message' => 'User deleted successfully']);
    //     } catch (\Exception $e) {
    //         Log::error('Delete user error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function update(Request $request, $id)
    {
        Log::info('Update user start', ['user_id' => $id]);

        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'company_id' => 'sometimes|exists:companies,id',
                'role' => 'sometimes|in:Admin,Manager,Employee',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            Log::info('Update user success', ['user_id' => $id]);

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            Log::error('Update user error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
