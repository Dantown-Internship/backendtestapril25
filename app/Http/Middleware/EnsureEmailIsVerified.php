<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\ApiResponse;

class EnsureEmailIsVerified
{
    use ApiResponse;

    public function handle($request, Closure $next)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            return $this->error('Your email address is not verified.', 409);
        }

        return $next($request);
    }
    
} 