<?php

namespace App\Exceptions;

use App\Http\Controllers\Concerns\HasApiResponse;
use Exception;

class CustomException extends Exception
{
    use HasApiResponse;

    public $errors = [];

    public function __construct(string $message = '', array $errors = [], int $statusCode = 400)
    {
        $this->errors = $errors;
        parent::__construct($message, $statusCode);
    }

    public function render()
    {
        return $this->errorResponse(
            $this->getMessage(),
            $this->getCode(),
            $this->errors,
        );
    }
}
