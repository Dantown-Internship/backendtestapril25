<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Compare the user's role against allowed roles using the Enum's value.
        if (!$user || !in_array($user->role->value, $roles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }
        return $next($request);
    }
}
