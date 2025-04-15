<?php

namespace App\Exceptions;

use Log;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $exception, Request $request)
    {
        if ($request->expectsJson()) {
            // Custom JSON response for API requests
            $statusCode = 500;
            $mes = 'An unexpected error occurred. Try again';
            $response = [
                'status' => 'error',
                'message' => $mes,
            ];

            // Customize the response based on the exception type
            if ($exception instanceof HttpException) {
                $statusCode = $exception->getStatusCode();
                $response['status'] = 'error';
                $response['message'] = $exception->getMessage();
            } elseif ($exception instanceof ValidationException) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            } elseif ($exception instanceof ModelNotFoundException) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Resource not found.',
                ], 404);
            } elseif ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authentication required. Login to continue',
                ], 401);
            } elseif ($exception instanceof AuthorizationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This action is unauthorized.',
                ], 403);
            } else {
                // Log the exception and return a generic error message
                Log::error($exception);
                $response = [
                    'status' => 'error',
                    'message' => app()->environment('local') ? $exception->getMessage() : $mes,
                ];

            }

            return response()->json($response, $statusCode);
        }

        // return response()->view('errors.500', [], 500);
    }
}
