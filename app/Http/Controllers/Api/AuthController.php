<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($request->only(['email', 'password']))) {
            return $this->unauthorized('Invalid credentials');
        }

        $user = User::find(Auth::id());

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Login successful');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'company_email' => ['required', 'email', 'unique:companies,email'],
            'company_name' => ['required', 'string', 'max:255', 'unique:companies,name'],
        ]);

        DB::transaction(function () use ($validated, &$user, &$token) {
            $company = Company::create([
                'name' => $validated['company_name'],
                'email' => $validated['company_email'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'Admin',
                'company_id' => $company->id,
            ]);

            $token = $user->createToken('accessToken')->plainTextToken;
        });

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Admin user for the company registered successfully', 201);

    }
}
