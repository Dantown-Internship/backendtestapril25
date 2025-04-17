<?php

namespace App\Helpers;

trait ResponseHelper
{

    public function success(array|null $data = null, string $message = 'Success', int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function error(string $message = 'Error', int $statusCode = 400, array|null $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    public function notFound(string $message = 'Resource not found')
    {
        return $this->error($message, 404);
    }

    public function unauthorized(string $message = 'Unauthorized')
    {
        return $this->error($message, 401);
    }

    public function forbidden(string $message = 'This action is unauthorised')
    {
        return $this->error($message, 403);
    }
} 