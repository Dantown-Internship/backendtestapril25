<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(2, true),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'category' => $this->faker->randomElement(['Travel', 'Food', 'Office Supplies']),
            'user_id' => User::factory(), // optional: will be overridden in tests
            'company_id' => 1, // replace or override in test
        ];
    }
}
