<?php

namespace App\Http\Middleware;

use App\Models\Expense;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            // Ensure all queries are scoped to the user's company
            $companyId = $request->user()->company_id;

            // Scope models if needed
            Expense::addGlobalScope('company', function ($builder) use ($companyId) {
                $builder->where('company_id', $companyId);
            });

            // Similarly for other models
        }

        return $next($request);
    }
}
