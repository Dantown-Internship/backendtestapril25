<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ExpenseData extends Data
{
    public function __construct(
        public int $company_id,
        public int $user_id,
        public string $title,
        public float $amount,
        public string $category,
    ) {}

    public static function fromRequest(array $validatedData): ExpenseData
    {
        $user = auth()->user();
        return new self(
            company_id: $user->company_id,
            user_id: $user->id,
            title: $validatedData['title'],
            amount: $validatedData['amount'],
            category: $validatedData['category']
        );
    }
}
