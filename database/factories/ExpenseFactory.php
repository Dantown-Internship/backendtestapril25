<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'amount' => fake()->numberBetween(1000, 100000), // Stored as cents
            'category' => fake()->randomElement(ExpenseCategory::cases())->value,
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
