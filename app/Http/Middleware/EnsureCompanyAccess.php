<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Get the company ID from the request parameter or from the user's company
        $companyId = $request->route('company_id') ?? $request->input('company_id');

        // If a specific company is being accessed, ensure the user belongs to that company
        if ($companyId && $user->company_id != $companyId) {
            abort(403, 'You do not have access to this company.');
        }

        return $next($request);
    }
}
