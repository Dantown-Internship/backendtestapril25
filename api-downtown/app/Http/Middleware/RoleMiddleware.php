<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must be logged in to access this resource.',
                'errors' => []
            ], 401);
        }

        $user = Auth::user();
        $userRole = strtolower($user->role);
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: Insufficient role permissions.',
                'errors' => []
            ], 403);
        }

        return $next($request);
    }
}