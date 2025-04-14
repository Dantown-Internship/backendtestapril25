<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    public function handle($request, Closure $next)
    {
        // Generate a unique cache key based on the request URI and method
        $cacheKey = 'cache_' . md5($request->fullUrl());

        // Check if the response is cached
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Get the response from the next middleware/controller
        $response = $next($request);

        // Store the response in the cache
        Cache::put($cacheKey, $response->getOriginalContent(), 60); // Cache for 60 seconds

        return $response;
    }
}