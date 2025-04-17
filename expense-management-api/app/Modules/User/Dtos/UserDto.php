<?php

namespace App\Modules\User\Dtos;

use App\Dtos\BaseDto;
use App\Enums\Roles;

readonly class UserDto extends BaseDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $company_id,
        public readonly ?Roles $role = Roles::EMPLOYEE,
        
    ) {}
}
