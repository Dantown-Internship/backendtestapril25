<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // If the request has a company_id parameter, verify it matches the user's company
        if ($request->has('company_id') && $request->company_id != $user->company_id) {
            abort(403, 'Unauthorized access to company data');
        }

        return $next($request);
    }
} 