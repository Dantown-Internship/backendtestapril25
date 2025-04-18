<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedAccessException extends Exception
{
    public function __construct($message = 'Unauthorized access to requested resource', $code = 403)
    {
        parent::__construct($message, $code);
    }
} 