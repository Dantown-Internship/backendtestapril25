<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class CompanyData extends Data
{
    public function __construct(
        #[Required]
        #[Max(255)]
        public string $name,

        #[Required]
        #[Max(255)]
        #[Unique('companies', 'email')]
        public string $email,
    ) {}
}
