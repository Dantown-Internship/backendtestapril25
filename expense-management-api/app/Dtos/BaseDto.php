<?php

namespace App\Dtos;

readonly class BaseDto
{
    public function __construct(...$args)
    {
    }

    /**
     * Get the request array representation of the data
     */
    public static function fromArray(array $data)
    {
        return new static(...$data);
    }
}