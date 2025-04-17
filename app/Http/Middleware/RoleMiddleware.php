<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Http\Controllers\Concerns\HasApiResponse;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use RuntimeException;
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
        $allowedRoles = [];
        foreach ($roles as $role) {
            $roleEnum = Role::tryFrom($role);
            if ($roleEnum === null) {
                throw new RuntimeException("Invalid role: {$role}");
            }
            $allowedRoles[] = $roleEnum;
        }

        throw_if(
            !$request->user() || !in_array($request->user()->role, $allowedRoles),
            new AuthorizationException('You do not have the required role to access this resource.')
        );

        return $next($request);
    }
}
