<?php

namespace App\Data;

use App\Enums\RoleEnum;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        #[Required]
        public string $name,

        #[Required]
        #[Unique('users', 'email')]
        public string $email,

        #[Required]
        public string $password,

        #[Required]
        #[Exists('companies', 'id')]
        public int $company_id,

        #[Required]
        #[Enum(RoleEnum::class)]
        public string $role,
    ) {}
}
