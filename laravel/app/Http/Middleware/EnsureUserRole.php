<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!in_array(auth()->user()->role, $roles)) {
            return ResponseHelper::unauthorized();
        }

        return $next($request);
    }
}
