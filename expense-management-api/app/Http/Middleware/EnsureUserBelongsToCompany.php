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
        if ($request->route('expense')) {
            $expense = $request->route('expense');

            //check if auth user company is thesame as the expense company
            if ($request->user()->company_id !== $expense->company_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
        return $next($request);
    }
}
