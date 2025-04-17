<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verify if the authenticated user has one of the required roles.
     *
     * This middleware accepts parameters to check if the user
     * has at least one of the specified roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  One or more roles to check against
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Ensure user is authenticated (fallback if auth middleware hasn't run)
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = Auth::user();
        
        // Verify user has one of the required roles
        if ($this->userHasRequiredRole($user, $roles)) {
            return $next($request);
        }
        
        // Access denied if user's role isn't in the allowed roles list
        return response()->json([
            'message' => 'Forbidden - Insufficient permissions'
        ], 403);
    }
    
    /**
     * Check if user has any of the required roles.
     *
     * @param  \App\Models\User|null  $user
     * @param  array  $roles
     * @return bool
     */
    private function userHasRequiredRole($user, array $roles): bool
    {
        return $user && 
               isset($user->role) && 
               in_array($user->role, $roles);
    }
}