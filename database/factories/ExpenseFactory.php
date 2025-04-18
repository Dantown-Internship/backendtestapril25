<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        $user = User::factory()->create(); // Ensure user exists

        return [
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->numberBetween(100, 10000),
            'category' => $this->faker->randomElement(['Food', 'Transport', 'Utilities', 'Misc']),
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ];
    }
}