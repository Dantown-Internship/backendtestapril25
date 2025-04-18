<?php

namespace App\Http\Middleware;

use App\Cache\ResponseCacheManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class CacheResponseMiddleware
{
    private $responseCacheManager;
    public function __construct(ResponseCacheManager $responseCacheManager)
    {
        $this->responseCacheManager = $responseCacheManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() === 'GET') {
            $cachedResponse = $this->responseCacheManager->getCachedResponse($request->fullUrl());
            if ($cachedResponse) {
                return response()->json($cachedResponse, HttpResponse::HTTP_OK);
            }

            $response = $next($request);

            $this->cacheResponseIfSuccessful($request, $response);

            return $response;
        }

        return $next($request);
    }

    private function cacheResponseIfSuccessful($request, $response)
    {
        if ($response->isSuccessful()) {
            $this->responseCacheManager->cacheResponse($request->fullUrl(), $response->getContent());
        }
    }
}
