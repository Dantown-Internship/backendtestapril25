<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'email', 'max:255', 'unique:companies,email'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Password::defaults()],
        ]);

        // Create company record
        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
        ]);

        // Create admin user record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);

        // Send verification email
        $user->notify(new VerifyEmailNotification);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'company' => $company,
            'token' => $token,
        ], 'Registration successful. Please check your email for verification link.', 201);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::find($request->id);

        if (! $user) {
            return $this->error('Invalid user', 404);
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), $request->hash)) {
            return $this->error('Invalid verification link', 400);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 400);
        }

        $user->markEmailAsVerified();

        return $this->success(null, 'Email verified successfully');
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->error('Email already verified', 400);
        }

        $request->user()->notify(new VerifyEmailNotification);

        return $this->success([
            'email' => $request->user()->email,
            'sent_at' => now()
        ], 'Verification link sent');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email has not been verified. Please check your inbox or junk mail for the verification link.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function getProfile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('company')
        ]);
    }
}