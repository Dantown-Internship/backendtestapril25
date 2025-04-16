<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure the authenticated user belongs to a company
        $user = $request->user();

        if (!$user || !$user->company_id) {
            return response()->json([
                'message' => 'User does not belong to any company'
            ], 403);
        }

        return $next($request);
    }
}
