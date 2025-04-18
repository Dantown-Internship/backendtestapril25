<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CheckTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        $token = PersonalAccessToken::findToken($request->bearerToken());

        if ($token && $token->expires_at && $token->expires_at->isPast()) {
            // Delete the expired token
            $token->delete();

            // Attempt to refresh the token
            $newToken = $this->refreshToken($user);

            if ($newToken) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Token refreshed',
                    'data' => ['token' => $newToken],
                ], 200)->header('Authorization', 'Bearer ' . $newToken);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired and could not be refreshed! Please, login again',
            ], 401);
        }

        return $next($request);
    }

    protected function refreshToken($user)
    {
        // Create a new token with a 24-hour expiration
        return $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;
    }
}