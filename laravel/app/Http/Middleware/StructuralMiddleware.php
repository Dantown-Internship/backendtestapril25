<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StructuralMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::debug("Request data: ", $request->all());

        $request->headers->set("Accept", "application/json");
        $request->headers->set("Content-Type", "application/json");

        $response = $next($request);

        Log::debug("Response status: ", [
            "status" => $response->getStatusCode(),
        ]);

        $response->headers->set("Accept", "application/json");
        $response->headers->set("Content-Type", "application/json");

        return $response;
    }
}
