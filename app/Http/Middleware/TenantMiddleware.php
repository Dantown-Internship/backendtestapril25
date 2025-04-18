<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->getHost();
        $tenant = Tenant::where('domain', $domain)->first();

        if (!$tenant) {
            abort(403, 'Tenant not found.');
        }

        // Bind the tenant so you can use app('tenant') later
        app()->instance('tenant', $tenant);

        return $next($request);
    }
}

