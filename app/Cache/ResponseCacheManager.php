<?php

namespace App\Cache;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ResponseCacheManager
{

    public function getCacheKey(string $url): string
    {

        if (Auth::check()) {
            return sprintf('response_cache:%s:%s', Auth::user()->company_id, $this->getURLPath($url));
        }

        return 'response_cache:' . $this->getURLPath($url);
    }

    public function cacheResponse(string $url, $response): void
    {
        $cacheKey = $this->getCacheKey($url);
        Cache::set($cacheKey, $response, now()->addHours(24));
    }

    public function getCachedResponse(string $url): ?array
    {
        $cacheKey = $this->getCacheKey($url);
        $cachedData = Cache::get($cacheKey);
        return $cachedData ? json_decode($cachedData, true) : null;
    }

    public function clearCache(string $url): void
    {
        $cacheKey = $this->getCacheKey($url);
        Cache::forget($cacheKey);
    }

    private function getURLPath(string $url): string
    {
        $parsed = parse_url($url);

        $path = ltrim($parsed['path'] ?? '/', '/');

        if (isset($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }

        return $path;
    }
}
