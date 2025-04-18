<?php

namespace App\DataTransferObjects;

class TokenDto
{
    public function __construct(
        public string $accessToken,
        public string $tokenType = 'Bearer',
        public ?int $expiresIn = null,
    ) {
        $this->expiresIn = config('sanctum.expiration') ? config('sanctum.expiration') * 60 : null;
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
        ];
    }
}
