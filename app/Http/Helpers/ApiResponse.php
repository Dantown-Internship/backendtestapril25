<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Request successful', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data'        => $data,
            'status'      => 'success',
            'message'     => $message,
            'api_version' => '1.0'
        ], $statusCode);
    }

    public static function error(string $errorType, string $message = 'An error occurred', int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'error' => [
                'type'    => $errorType,
                'message' => $message,
                'status'  => 'failed'
            ]
        ], $statusCode);
    }
}
