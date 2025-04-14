<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{

    public function handle($request, Closure $next, ...$roles): Response {
        if (!in_array(auth()->user()->role, $roles)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $next($request);
    }
}
