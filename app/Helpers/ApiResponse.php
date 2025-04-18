<?php

/** Custom Api response */
if (!function_exists('api_response')) {
    function api_response($data = null, $message = '', $success = true, $status = 200)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}