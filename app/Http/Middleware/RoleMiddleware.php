<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Traits\HasApiResponse;
use Closure;
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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()
            || ! $this->userHasRole($request->user(), $roles)
        ) {
            return $this->errorResponse(
                message: 'You do not have the required role to access this resource.',
                statusCode: 403,
            );
        }

        return $next($request);
    }

    protected function getAllowedRoles(array $roles)
    {
        $allowedRoles = [];
        foreach ($roles as $role) {
            $roleEnum = Role::tryFrom($role);
            if ($roleEnum === null) {
                throw new RuntimeException("Invalid role: {$role}");
            }
            $allowedRoles[] = $roleEnum;
        }

        return $allowedRoles;
    }

    protected function userHasRole($user, $roles)
    {
        return in_array($user->role, $this->getAllowedRoles($roles));
    }
}
