<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $exception, Request $request)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($exception);
        }
    }

    /**
     * Handles exceptions specifically for API requests, returning a JSON response.
     *
     * @param  Throwable  $exception
     */
    protected function handleApiException(Exception $exception): JsonResponse
    {
        // Custom JSON response for API requests
        $statusCode = $exception->getCode() ?? 500;
        $mes = 'An unexpected error occurred. Try again';
        $response = [
            'success' => false,
            'message' => $mes,
        ];

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $response['success'] = false;
            $response['message'] = $exception->getMessage();
        } elseif ($exception instanceof ValidationException) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } elseif ($exception instanceof ModelNotFoundException) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Resource not found.',
            ], 404);
        } elseif ($exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Login to continue',
            ], 401);
        } elseif ($exception instanceof ThrottleRequestsException) {
            return response()->json([
                'success' => false,
                'message' => 'Too Many Requests.',
            ], 429);
        } elseif ($exception instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => 'This action is unauthorized.',
            ], 403);
        } else {
            // Log the exception
            Log::error($exception);
            $response = [
                'success' => false,
                'message' => app()->environment('local') ? $exception->getMessage() : $mes,
            ];
        }

        return response()->json($response, $statusCode);
    }
}
