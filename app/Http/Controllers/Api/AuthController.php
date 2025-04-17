<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password',
            ], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        // Generate token
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }
    
}
