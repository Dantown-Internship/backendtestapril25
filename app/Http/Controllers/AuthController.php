<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'name' => 'required',
        ]);

        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->email,
        ]);

        $user = $company->users()->create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'Admin',
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully', 'token' => $user->createToken('api-token')->plainTextToken], 200);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['message' => 'login Successfull','token' => $user->createToken('api-token')->plainTextToken],200);
    }
}
