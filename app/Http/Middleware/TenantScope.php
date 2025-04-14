<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantScope
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            view()->share('company_id', Auth::user()->company_id);
            $request->attributes->add(['company_id' => Auth::user()->company_id]);
        }

        return $next($request);
    }
}
