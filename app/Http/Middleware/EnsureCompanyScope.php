<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyScope
{
    public function handle(Request $request, Closure $next)
    {
        // Apply company scope to all requests
        // This ensures users can only access data from their own company
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Add company_id to the request for controllers to use
        $request->merge(['company_id' => $user->company_id]);

        return $next($request);
    }
}