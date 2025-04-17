<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceAdminAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $loggedInUser = auth('sanctum')->user();

        if ($loggedInUser->role !== 'Admin') {
            return generateErrorApiMessage('Only Admins can perform this action', 403);
        }

        return $next($request);
    }
}
