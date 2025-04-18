<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {     
        $user = auth()->user();
        $model = User::all();

        if (!$user->role === 'Admin' && $user && $user->company_id !== $model->company_id) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
