<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Http\Controllers\Concerns\HasApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use HasApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,  ...$roles): Response
    {
        $rolesArray = [];
        foreach ($roles as $role) {
            $roleEnum = Role::tryFrom($role);
            if (!$role) {
                return $this->errorResponse("Invalid role: $role", [], 500);
            }
            $rolesArray[] = $roleEnum;
        }

        if (!$request->user() || !in_array($request->user()->role, $rolesArray)) {
            return $this->errorResponse('Unauthorized', [], 403);
        }

        return $next($request);
    }
}
