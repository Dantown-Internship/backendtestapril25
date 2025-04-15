<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    public function handle($request, Closure $next)
    {
        $cacheKey = 'cache_' . md5($request->fullUrl());
        $fingerprintKey = $cacheKey . '_fingerprint';

        // Check if we already have cached content and fingerprint
        if (Cache::has($cacheKey) && Cache::has($fingerprintKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Proceed to get the actual response
        $response = $next($request);

        // Get the response data as array or string
        $responseData = $response->getOriginalContent();

        // You can encode to JSON to ensure uniform structure for hashing
        $responseJson = json_encode($responseData);

        // Generate a hash from the response
        $currentFingerprint = md5($responseJson);

        // Cache the response and fingerprint
        Cache::put($cacheKey, $responseData, 60);
        Cache::put($fingerprintKey, $currentFingerprint, 60);

        return $response;
    }
}
