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
        // Check if the user is authenticated and belongs to a company
        $user = $request->user();

        if (!$user || !$user->company_id) {
            return response()->json([
                'message' => 'Unauthorized. User must belong to a company.'
            ], 403);
        }

        // Check for company ID in the route parameters (for expense, user, etc. endpoints)
        $companyId = $request->route('company_id');
        if ($companyId && $companyId != $user->company_id) {
            return response()->json([
                'message' => 'Unauthorized. Cannot access resources from another company.'
            ], 403);
        }

        return $next($request);
    }
}
