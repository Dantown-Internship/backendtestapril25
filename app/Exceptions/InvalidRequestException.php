<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidRequestException extends Exception
{
    protected string $errorType = 'invalid_request_error';
    protected int $statusCode;

    public function __construct(string $message = 'Invalid request', int $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'type'    => $this->errorType,
                'message' => $this->getMessage(),
                'status'  => 'failed',
            ]
        ], $this->statusCode);
    }
}
