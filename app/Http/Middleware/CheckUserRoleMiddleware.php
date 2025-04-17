<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // get user role and company
        $user = User::find(auth('api')->user()->id);
        $roleEnum = UserRoleEnum::from($role);

        // dd($user->role, $roleEnum->name);

        if($user->role !== $roleEnum->name){
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
