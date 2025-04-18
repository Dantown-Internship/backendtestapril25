<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
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
            'company_id' => Company::factory()->create(),
            'user_id' => User::factory()->create(),
            'title' => $this->faker->words(3, true),
            'category' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(2, 100, 100000),
        ];
    }
}
