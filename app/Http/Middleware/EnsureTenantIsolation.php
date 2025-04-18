<?php

namespace App\Http\Middleware;

use App\Models\Expense;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsolation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }
        
        if ($request->route('expense')) {
            $expense = Expense::findOrFail($request->route('expense'));
            if ($expense->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }
        }
        
        if ($request->route('user')) {
            $requestedUser = User::findOrFail($request->route('user'));
            if ($requestedUser->company_id !== $user->company_id) {
                return response()->json([
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }
        }
        
        if ($request->is('api/expenses') || $request->is('api/users')) {
            $request->merge(['company_id' => $user->company_id]);
        }
        
        return $next($request);
    }
}
