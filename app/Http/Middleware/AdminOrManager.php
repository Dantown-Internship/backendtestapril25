<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Ensure the user exists and has a valid role
        if (!$user || !in_array($user->role, ['Admin', 'Manager'])) {
            return response()->json([
                'message' => 'Unauthorized, Admin and Manager Access Only.'
            ], 403);
        }

        return $next($request);
    }
}