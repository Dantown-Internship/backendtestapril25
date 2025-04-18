<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseFormatterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Success response with mandatory message and optional data
        Response::macro('success', function ($message, $data = null, $statusCode = 200) {
            $response = [
                'message' => $message,
            ];

            if (!is_null($data)) {
                $response['data'] = $data;
            }

            return Response::json($response, $statusCode);
        });

        // Error response with mandatory message and optional data
        Response::macro('error', function ($message, $data = null, $statusCode = 400) {
            $response = [
                'message' => $message,
            ];

            if (!is_null($data)) {
                $response['data'] = $data;
            }

            return Response::json($response, $statusCode);
        });

        // Not found response
        Response::macro('notFound', function ($message = 'Resource not found') {
            return Response::json([
                'message' => $message,
            ], 404);
        });

        // Unauthorized response
        Response::macro('unauthorized', function ($message = 'Unauthorized') {
            return Response::json([
                'message' => $message,
            ], 403);
        });
    }
}
