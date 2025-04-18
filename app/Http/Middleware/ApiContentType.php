<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiContentType
{
    public function handle(Request $request, Closure $next): Response
    {

        // Check for the Content-Type header
        if ($request->header('Content-Type') !== 'application/json') {
            return response()->json(['message' => 'Invalid Content-Type header'], 406);
        }

        // Check for the Accept header
        if ($request->header('Accept') !== 'application/json') {
            return response()->json(['message' => 'Invalid Accept header'], 406);
        }

        return $next($request);
    }
}
