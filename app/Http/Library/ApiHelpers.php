<?php

namespace App\Http\Library;

use Illuminate\Http\JsonResponse;

trait ApiHelpers
{
    protected function isAdmin($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('Admin');
        }

        return false;
    }

    protected function isManager($user): bool
    {

        if (!empty($user)) {
            return $user->tokenCan('Manager');
        }

        return false;
    }

    protected function isEmployee($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('Employee');
        }

        return false;
    }


    protected function onSuccess($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function onError(int $code, string $message = ''): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
        ], $code);
    }


}