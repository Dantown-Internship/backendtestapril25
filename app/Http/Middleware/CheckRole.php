<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Split roles by "|" (pipe) to support multiple roles
        $roleArray = explode('|', $roles);

        if(!in_array($request->user()->role, $roleArray))
        {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
