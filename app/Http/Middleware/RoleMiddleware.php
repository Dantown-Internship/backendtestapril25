<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Check if user has one of the required roles.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Ensure the user exists and has the 'Admin' role
        if (!$user || $user->role !== 'Admin') {
            return response()->json([
                'message' => 'Unauthorized, Only Admin have access.'
            ], 403);
        }

        return $next($request);
    }
}