<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }
        
        if (empty($roles)) {
            return $next($request);
        }
        
        $userRole = strtolower($user->role);
        
        foreach ($roles as $role) {
            if ($userRole === strtolower($role)) {
                return $next($request);
            }
        }
        
        return response()->json([
            'message' => 'You do not have permission to access this resource.'
        ], 403);
    }
}
