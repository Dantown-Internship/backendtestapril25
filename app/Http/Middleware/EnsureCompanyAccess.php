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
        
        // Check if the company_id in the route or request matches the user's company_id
        $companyId = $request->route('company_id') ?? $request->input('company_id');
        
        if ($companyId && $user->company_id != $companyId) {
            return response()->json([
                'message' => 'Unauthorized. You can only access data from your own company.'
            ], 403);
        }
        
        // If company_id wasn't specified in the route or request, add it to the request
        if (!$companyId && $user->company_id) {
            $request->merge(['company_id' => $user->company_id]);
        }
        
        return $next($request);
    }
}
