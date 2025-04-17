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
        $users = User::where('company_id', auth()->user()->company_id)
            ->get();
        Log::info('Users listed', ['admin_id' => auth()->id(), 'count' => $users->count()]);
        return response()->json([
                           'status'=>true,
                           'data'=> $users
                        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:Admin,Manager,Employee'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => auth()->user()->company_id,
            'role' => $data['role']
        ]);

        Log::info('User created by admin', ['user_id' => $user->id, 'admin_id' => auth()->id()]);
        return response()->json([
            'status'=>true,
            'data'=> $user
         ],201);
    }

    public function update(Request $request, $userId)
    {
        $user = User::find($userId);
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:Admin,Manager,Employee'
        ]);


        if (!$user) {
            Log::warning('User not found for update attempt', [
                'admin_id' => auth()->id(),
                'target_user_id' => $userId
            ]);
            return response()->json([
                'status' => false,
                'error' => 'User not found'
            ], 404);
        }

        if ($user->company_id !== auth()->user()->company_id) {
            Log::warning('Unauthorized user update attempt', [
                'admin_id' => auth()->id(),
                'target_user_id' => $user->id
            ]);
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $data = $request->validate([
            'role' => 'required|in:Admin,Manager,Employee'
        ]);

        $user->update(['role' => $data['role']]);
        Log::info('User role updated', [
            'user_id' => $user->id,
            'new_role' => $data['role'],
            'admin_id' => auth()->id()
        ]);

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }

}