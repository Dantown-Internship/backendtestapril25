<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckAdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        $user = Auth::user();

        // If a user is logged in and they are an Admin — block registration
        if ($user && $user->role === 'Admin') {
            return response()->json([
                'message' => 'An Admin already logged in. Please log out to register a new one'
            ], 403);
        }

        return $next($request);
    }
}
