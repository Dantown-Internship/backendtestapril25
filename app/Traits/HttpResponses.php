<?php
namespace App\Traits;
trait HttpResponses {
    protected function success($data, $message = 'Request successful.', $code = 200) {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    // Generic Error Response
    protected function error($message = 'An error occurred.', $code = 500, $data = []) {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    // Specific Error Methods
    protected function notFound($message = 'Resource not found.') {
        return $this->error($message, 404);
    }

    protected function unauthorized($message = 'Unauthorized access.') {
        return $this->error($message, 401);
    }

    protected function forbidden($message = 'Forbidden.') {
        return $this->error($message, 403);
    }




    protected function badRequest($message = 'Bad request.') {
        return $this->error($message, 400);
    }

    // Invalid Request for Specific Needs
    protected function invalidRequest($message = 'Invalid request.') {
        return $this->error($message, 422);
    }
}
