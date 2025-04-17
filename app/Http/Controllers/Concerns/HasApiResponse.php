<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasApiResponse
{
    protected function successResponse(string $message = 'Success', Responsable|array|null $data = null, int $statusCode = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];
        if (! blank($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);

    }

    protected function errorResponse(string $message = 'Error', int $statusCode = 400, ?array $errors = null)
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if (! blank($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    protected function paginatedResponse(string $message, LengthAwarePaginator|AnonymousResourceCollection $data, int $statusCode = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                    'has_more_pages' => $data->hasMorePages(),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                    'current_page_url' => $data->url($data->currentPage()),
                    'path' => $data->path(),
                ],
            ],
        ], $statusCode);
    }
}
