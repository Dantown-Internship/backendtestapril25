<?php

namespace App\Traits;

trait ApiResponse
{
    public static function success($data, $message = "Success", $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function failure($message = "failure", $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}
