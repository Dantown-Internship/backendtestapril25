<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAutoWrapper
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // If the response is already formatted or is an error, don't modify it
        if (
            $response instanceof JsonResponse &&
            ($response->getData(true)['status'] ?? null) === 'success'
        ) {
            return $response;
        }

        // Wrap if response is successful and not already structured
        if ($response->isSuccessful() && $response instanceof JsonResponse) {
            $original = $response->getData();

            return response()->apiSuccess(
                data: $original,
                message: 'Request successful',
                statusCode: $response->status()
            );
        }

        // Let all other responses (errors, non-JSON, etc.) pass through untouched
        return $response;
    }
}
