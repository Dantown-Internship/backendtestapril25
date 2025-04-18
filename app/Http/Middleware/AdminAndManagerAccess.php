<?php

namespace App\Http\Middleware;

use App\Constants\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAndManagerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role === UserRole::EMPLOYEE) {
            return response()->json(['message' => 'Only admins and managers can perform this action'], 403);
        }

        if ($user->role === UserRole::MANAGER && $user->company_id !== (int) $request->route('id')) {
            return response()->json(['message' => 'Managers can not manage another comapny\'s expense'], 403);
        }

        return $next($request);
    }
}
