<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowAdminOrManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->authenticated_user;

        if (!$user || $user->isAdminOrManager() === false) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action',
            ], 403);
        }
        return $next($request);
    }
}
