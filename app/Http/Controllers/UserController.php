<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{   
    public function index(Request $request)
{
    $admin = $request->user();

    $users = User::where('company_id', $admin->company_id)
                ->orderBy('created_at', 'desc')
                ->get();

    return response()->json($users);
}

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


public function store(Request $request)
    {
        try {
            $this->validateUserRequest($request);
           
            $user = User::firstOrCreate(
                [
                'email' => $request->input('email')
                ],
                [
                'name' => $request->input('name'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
                ]);
            $isNewEntry = $user->wasRecentlyCreated;

            if ($isNewEntry) {
                return response()->json(['message' => 'New user created.'], 200);
            } else {
                
                return response()->json([ 'message' => 'User already exists.'], 422);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage()], 422);
        }
    }
    

    private function validateUserRequest(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Admin,Manager,Employee',
        ]);
    
        if (!$validator) {
            print $validator->errors();
            return response()->json([
                            'errors' => $validator->errors()
                        ], 422);
        }
    }

    public function login(Request $request)
{
    try {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Create a new Sanctum token
        $token = $user->createToken($request->email)->plainTextToken;

        // Optional: Save token to DB (for refresh, revoke, etc.)
        $user->update(['refresh_token' => $token]); // Ensure you have this column

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id)
{
    $admin = $request->user();

    $validated = $request->validate([
        'role' => 'required|in:Admin,Manager,Employee'
    ]);

    $user = User::where('company_id', $admin->company_id)->findOrFail($id);

    $user->update(['role' => $validated['role']]);

    return response()->json(['message' => 'User role updated', 'user' => $user]);
}


}
