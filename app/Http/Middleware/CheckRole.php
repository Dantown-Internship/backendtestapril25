<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        switch ($role) {
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Unauthorized. Admin access required.');
                }
                break;
            case 'manager':
                if (!$user->isManager() && !$user->isAdmin()) {
                    abort(403, 'Unauthorized. Manager or Admin access required.');
                }
                break;
            case 'employee':
                if (!$user->isEmployee() && !$user->isManager() && !$user->isAdmin()) {
                    abort(403, 'Unauthorized. Employee, Manager, or Admin access required.');
                }
                break;
        }

        return $next($request);
    }
} 