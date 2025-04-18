<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $companyId = $request->route('company_id') ?? $request->input('company_id') ?? $user->company_id;

        if ($user->company_id != $companyId) {
            return response()->json(['error' => 'Unauthorized access to company'], 403);
        }

        return $next($request);
    }
}