<?php

namespace App\Modules\Expense\Dtos;

use App\Dtos\BaseDto;

readonly class ExpenseDto extends BaseDto
{
    public function __construct(
        public readonly string $title,
        public readonly int $amount,
        public readonly string $category,
        public readonly string $user_id,
        public readonly string $company_id,
    ) {}
}
