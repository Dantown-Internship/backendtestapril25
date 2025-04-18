<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement(['Travel', 'Meals', 'Supplies', 'Other']),
            'company_id' => fn () => User::factory()->create()->company_id,
            'user_id' => fn () => User::factory()->create()->id,
        ];
    }
}
