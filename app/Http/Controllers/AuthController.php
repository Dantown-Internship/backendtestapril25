<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'company_name' => 'required|string|max:255', // New field
            'company_email' => 'required|email|unique:companies,email', // New field
            'role' => 'sometimes|in:Admin,Manager,Employee' // Optional, defaults to Admin
        ]);

        $company = Company::create([
            'name' => $data['company_name'],
            'email' => $data['company_email'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $company->id,
            'role' => $data['role'] ?? 'Admin' // First user is Admin
        ]);

        Log::info('New user registered with company', [
            'user_id' => $user->id,
            'company_id' => $company->id
        ]);

        return response()->json([
            'status'=>true,
            'user' => $user,
            'company' => $company,
            'token' => $user->createToken('api')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        // Unchanged from previous implementation
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!auth()->attempt($credentials)) {
            Log::warning('Login failed', ['email' => $request->email]);
            return response()->json(['status'=>false,'error' => 'Unauthorized'], 401);
        }

        return response()->json(['status'=>true,'token' => auth()->user()->createToken('api')->plainTextToken]);
    }
}