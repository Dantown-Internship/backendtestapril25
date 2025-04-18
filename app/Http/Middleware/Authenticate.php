<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        // Return null to prevent redirect and allow Sanctum to return a JSON response
        return $request->expectsJson() ? null : null;
    }
}
