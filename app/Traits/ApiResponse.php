<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    public function successResponse($data = null, $message = 'Request successful', $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function errorResponse($message = 'An error occurred', $statusCode = 500, $errors = null)
    {   
        Log::error($errors);

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => config('app.debug') ? $errors : null,
        ], $statusCode);
    }

    public function notFoundResponse($message = 'The requested resource was not found', $statusCode = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}