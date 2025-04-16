<?php

namespace App\Traits;

trait HttpResponses
{
    public function success($data = null, $message = null, $code = 200, $responseCode = '00')
    {
        if (is_string($data) && is_null($message)) {
            $message = $data;
            $data = null;
        }

        $response = [
            'status' => 'success',
            'message' => $message,
            'responseCode' => $responseCode,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public function error($message, $code = 400, $responseCode = '01')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'responseCode' => $responseCode,
        ], $code);
    }
}
