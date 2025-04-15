<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$role): Response
    {
        $executingRole = $request->user()->role;

        if (!$request->user() || !in_array($executingRole, $role)) {
            return response()->json([
                'message' => "You cannot perform this action as a ". ucwords($executingRole) . " user",
                "success" => false
            ], 403);
        }
        
        return $next($request);
    }
}
