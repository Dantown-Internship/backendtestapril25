<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

trait ResponseTrait
{

    public function serverErrorResponse($message, ?\Throwable $throwable = null): JsonResponse
    {
        if ($throwable !== null) {
            Log::error(
                "{$throwable->getMessage()} on line {$throwable->getLine()} in {$throwable->getFile()}"
            );
        }

        $response = [
            'status' => false,
            'code' => 500,
            'message' => $message,
        ];

        return Response::json($response, 500);
    }

    public function successResponse(string $message = 'Operation successful', array $data = [], int $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function errorResponse(string $message = 'An error occurred', int $code = 500, array $data = [], ?\Throwable $throwable = null)
    {
        if ($throwable != null) {
            Log::error(
                "{$throwable->getMessage()} on line {$throwable->getLine()} in {$throwable->getFile()}"
            );
        }
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function badRequestResponse(string $message = '', int $code = 400, array $data = [], ?\Throwable $throwable = null)
    {
        if ($throwable != null) {
            Log::error(
                "{$throwable->getMessage()} on line {$throwable->getLine()} in {$throwable->getFile()}"
            );
        }

        $defaultMessage = 'The request could not be processed due to invalid or missing data.';

        return response()->json([
            'status' => false,
            'message' => !empty($message) ? $message : $defaultMessage,
            'data' => $data,
        ], $code);
    }

    public function validationErrorResponse($errors, string $message = 'Validation failed')
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    public function notFoundResponse(string $message = 'Resource not found')
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 404);
    }

    public function unauthorizedResponse(string $message = 'Unauthorized')
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 401);
    }

    public function forbiddenResponse(string $message = 'Forbidden')
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 403);
    }

    public function noContentResponse()
    {
        return response()->json(null, 204);
    }
}
