<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Force the request to expect JSON response
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}