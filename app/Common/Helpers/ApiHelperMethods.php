<?php

function generateSuccessApiMessage($message = '', $statusCode = 200, $data = null)
{
    $responsePayload = [
        'status' => 'Success',
        'status_code' => $statusCode,
        'message' => $message,
    ];

    if ($data) {
        $responsePayload['data'] = $data;

    }

    return response()->json($responsePayload, $statusCode);
}

function generateErrorApiMessage($message = '', $statusCode = 400, $data = null)
{
    $responsePayload = [
        'status' => 'Error',
        'status_code' => $statusCode,
        'message' => $message,
    ];

    if ($data) {
        $responsePayload['data'] = $data;

    }

    return response()->json($responsePayload, $statusCode);
}
