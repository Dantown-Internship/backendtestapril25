<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{

    use HttpResponses;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();


        if (!$user || !$user->hasRole(RoleEnum::ADMIN)) {
            return $this->forbidden("forbidden");
        }

        return $next($request);
    }
}
