<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle model not found exceptions
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Resource not found.'
            ], 404);
        }

        // Handle route not found
        if ($e instanceof NotFoundHttpException || $e instanceof RouteNotFoundException) {
            return response()->json([
                'message' => 'Endpoint not found.'
            ], 404);
        }

        // Handle validation errors
        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }

        // For API responses, always return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            // Get appropriate status code
            $statusCode = 500;
            
            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            }
            
            if ($statusCode === 500) {
                $response = [
                    'message' => 'Server error occurred.'
                ];
                
                // In local environment, include more details
                if (config('app.debug')) {
                    $response['debug'] = [
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ];
                }
                
                return response()->json($response, $statusCode);
            }
            
            return response()->json([
                'message' => $e->getMessage()
            ], $statusCode);
        }

        return parent::render($request, $e);
    }
} 