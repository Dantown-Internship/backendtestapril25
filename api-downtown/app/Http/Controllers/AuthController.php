<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Mail\VerifyEmail;
use App\Models\Companies;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;




class AuthController extends Controller
{

 

    /**
     * Register a new user and company.
     */
    
    public function register(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|email|unique:companies,email',
        ]);

        if (Auth::check() && Auth::user()->role !== 'Admin') {
            return $this->errorResponse('You are unauthorized to perform this action', 403);
        }

        $company = Companies::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);

        // Generate the backend verification link
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'hash' => sha1($user->email)],
            absolute: true
        );

        // Wrap for frontend
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $frontendWrappedUrl = $frontendUrl . '/verify-email?' . http_build_query(['url' => $signedUrl]);

        // Save raw backend URL for Postman/debugging/testing
        $user->verification_link = $signedUrl;
        $user->save();

        // Send email with frontend link
        Mail::to($user->email)->send(new VerifyEmail($frontendWrappedUrl, $user));


        // Generate access token
        $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

        return $this->successResponse('Registration successful. Please verify your email to gain access to your account dashboard.', [
            'token' => $token,
            'user' => $user,
            'frontend_verification_link' => $frontendWrappedUrl, // convenience for frontend dev
            'backend_verification_link' => $signedUrl // for Postman testing
        ], 201);

    } catch (ValidationException $e) {
        return $this->errorResponse($e->getMessage(), 422, $e->errors());
    } catch (QueryException $e) {
        return $this->errorResponse('Failed to register user. Please try again.', 500);
    }
}





    /**
     * Log in a user.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return $this->errorResponse('Please verify your email address', 403);
            }

            $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

            return $this->successResponse('Login successful', [
                'token' => $token,
                'user' => $user
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }
    }


    /**
     * Refresh token method
     * invalidate and generate a new toke
     */
    // app/Http/Controllers/AuthController.php
public function refreshToken(Request $request)
{
    try {
        $user = $request->user();
        if (!$user) {
            return $this->errorResponse('Unauthenticated! You do not have access', 401);
        }

        // Revoke the current token
        $currentToken = PersonalAccessToken::findToken($request->bearerToken());
        if ($currentToken) {
            $currentToken->delete();
        }

        // Issue a new token
        $newToken = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

        return $this->successResponse('Token refreshed successfully', ['token' => $newToken]);
    } catch (\Exception $e) {
        return $this->errorResponse('Failed to refresh token', 500);
    }
}

    /**
     * Log out a user.
     */
    // public function logout(Request $request)
    // {
    //     try {
    //         $request->user()->currentAccessToken()->delete();
    //         return $this->successResponse('Logged out successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Failed to log out. Please try again.', 500);
    //     }
    // }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('User not authenticated.', 401);
            }
    
            //$token = PersonalAccessToken::findToken($request->bearerToken());
            $token = PersonalAccessToken::findToken($request->bearerToken());
            if ($token) {
                $token->delete();
            } else {
                return $this->errorResponse('Invalid or expired token.', 401);
            }
    
            return $this->successResponse('Logged out successfully');
        } catch (\Exception $e) {          
            Log::error('Logout Error: ' . $e->getMessage());
    
            return $this->errorResponse('An unexpected error occurred during logout: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Send a password reset link.
     */
    public function forgotPassword(Request $request)
{
    try {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        // Generate the password reset token
        $token = Password::createToken($user);

        // Create backend reset URL
        $resetUrl = URL::to('/') . '/reset-password?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]);

        // Save to user for testing/debugging
        $user->reset_password_link = $resetUrl; 
        $user->save();

        // Wrap for frontend consumption of the API
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $frontendWrappedUrl = $frontendUrl . '/reset-password?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]);

        // Send the reset email to the user
        Mail::to($user->email)->send(new ResetPasswordMail($frontendWrappedUrl, $user));


        return $this->successResponse('Reset link sent. Check your email.', [
            'backend_reset_link' => $resetUrl,         // For postman test
            'frontend_reset_link' => $frontendWrappedUrl // For frontend consumption of the endpoint
        ]);

    } catch (ValidationException $e) {
        return $this->errorResponse($e->getMessage(), 422, $e->errors());
    }
}


    /**
     * Reset the user's password.
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ]);
    
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();
    
                    // revoke old tokens
                    $user->tokens()->delete();
    
                    // generate new token
                    $user->fresh();
                }
            );
    
            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse('Your password has been reset successfully.');
            } else {
                return $this->errorResponse(__($status), 400);
            }
    
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }
    }
    
    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', 401);
            }

            $user->update(['password' => Hash::make($request->password)]);

            // Revoke all tokens for security
            $user->tokens()->delete();

            return $this->successResponse('Password updated successfully. Please log in again.');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        }
    }

    /**
     * Verify email address.
     */

    public function verifyEmail(Request $request)
    {
        try {
            $user = User::findOrFail($request->route('id'));
    
            // Verify the URL is signed (Laravel’s built-in protection)
            if (! URL::hasValidSignature($request)) {
                return $this->errorResponse('Invalid or expired verification link', 403);
            }
    
            // match hash for extra security
            $hash = $request->route('hash');
            if (! hash_equals((string) $hash, sha1($user->email))) {
                return $this->errorResponse('Invalid verification hash', 403);
            }
    
            if ($user->hasVerifiedEmail()) {
                return $this->successResponse('Email already verified');
            }
    
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
                return $this->successResponse('Email verified successfully');
            }
    
            return $this->errorResponse('Failed to verify email', 400);
    
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to verify email. Please try again.', 500);
        }
    }
    

    /**
     * Standardized success response.
     */
    protected function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Standardized error response.
     */
    protected function errorResponse(string $message, int $status, array $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}