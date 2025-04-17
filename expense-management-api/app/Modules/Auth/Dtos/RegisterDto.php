<?php

namespace App\Modules\Auth\Dtos;

use App\Dtos\BaseDto;
use App\Enums\Roles;

readonly class RegisterDto extends BaseDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $company_name,
        public readonly string $company_email,
        public ?Roles $role = null,
    ) {}
}
