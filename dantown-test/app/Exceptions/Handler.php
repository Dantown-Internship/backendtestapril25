<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {

        $this->renderable(function (Throwable $e, Request $request): JsonResponse {
            if ($request->wantsJson()) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    /**
     * Custom error handling for API requests.
     */
    private function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        $response = [
            'message' => 'Internal Server Error',
            'status' => 500, // Default status code
            'error_code' => 'ERR-001',
        ];

        // Customize the response based on the exception type
        if ($e instanceof HttpException) {
            $response['message'] = $e->getMessage() ?: 'HTTP Error';
            $response['status'] = $e->getStatusCode();
            $response['error_code'] = 'HTTP-' . $e->getStatusCode();
        } elseif ($e instanceof ValidationException) {
            $response['message'] = 'Validation Error';
            $response['status'] = 422;
            $response['errors'] = $e->errors();
            $response['error_code'] = 'VALIDATION-ERROR';
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            $response['message'] = 'Unauthenticated';
            $response['status'] = 401;
            $response['error_code'] = 'AUTHENTICATION_ERROR';
        } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            $response['message'] = 'Unauthorized';
            $response['status'] = 403;
            $response['error_code'] = 'AUTHORIZATION_ERROR';
        } else {
            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace(),
                ];
            }
        }
        // Consistent structure
        $responseData = [
            'success' => false,
            'message' => $response['message'],
            'data' => (object) [],
        ];

        if (isset($response['errors'])) {
            $responseData['errors'] = $response['errors'];
        }
          if (config('app.debug')) {
            $responseData['debug'] = $response['debug'];
        }

        return response()->json($responseData, $response['status']);
    }
}

