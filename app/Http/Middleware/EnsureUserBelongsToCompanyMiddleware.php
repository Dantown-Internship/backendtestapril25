<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expense = $request->route('expense');

        if ($expense && $expense->company_id !== companyID()) {
            return response()->json(['message' => 'Unauthorized access to expense'], 403);
        }

        return $next($request);
    }
}