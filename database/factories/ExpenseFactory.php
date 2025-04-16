<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'category' => $this->faker->randomElement(['Travel', 'Food', 'Equipment', 'Office']),
        ];
    }
}
