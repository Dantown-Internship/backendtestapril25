<?php

use Illuminate\Http\JsonResponse;

function successJsonResponse(string $message = '', mixed $data = null, int $code = 200): JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], $code);
}


function errorJsonResponse(string $message = '', mixed $errors = null, int $code = 422): JsonResponse
{
    return response()->json([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $code);
}
