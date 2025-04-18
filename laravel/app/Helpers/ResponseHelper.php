<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Build a standardized response.
     */
    private static function buildResponse(
        bool $status,
        string $message,
        mixed $data,
        int $statusCode,
        mixed $error = null
    ): JsonResponse {
        return response()->json([
            "status" => $status,
            "statusCode" => $statusCode,
            "message" => $message,
            "data" => $data,
            "error" => $error,
        ], $statusCode);
    }

    public static function success(
        mixed $data = [],
        string $message = "Successful",
        int $statusCode = 200,
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(true, $message, $data, $statusCode, $error);
    }

    public static function error(
        string $message = "An error occurred",
        int $statusCode = 400,
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, $statusCode, $error);
    }

    public static function created(
        mixed $data = [],
        string $message = "Resource created successfully",
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(true, $message, $data, 201, $error);
    }

    public static function updated(
        mixed $data = [],
        string $message = "Resource updated successfully",
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(true, $message, $data, 200, $error);
    }

    public static function internalServerError(
        string $message = "Internal Server Error",
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, 500, $error);
    }

    public static function unauthorized(
        string $message = "Unauthorized",
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, 401, $error);
    }

    public static function unauthenticated(
        string $message = "Unauthenticated",
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, 403, $error);
    }

    public static function forbidden(
        string $message = "Forbidden",
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, 403, $error);
    }

    public static function notFound(
        string $message = "Resource not found",
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, 404, $error);
    }

    public static function unprocessableEntity(
        string $message = "Unprocessable entity",
        mixed $data = [],
        mixed $error = null
    ): JsonResponse {
        return self::buildResponse(false, $message, $data, 422, $error);
    }

    /**
     * Implode nested arrays to strings.
     */
    public static function implodeNestedArrays(
        mixed $array,
        string $separator = ", "
    ): array {
        return array_map(function ($value) use ($separator) {
            return is_array($value) ? implode($separator, $value) : $value;
        }, $array);
    }
}
