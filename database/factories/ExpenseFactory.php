<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Company;

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
            'title' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'category' => fake()->randomElement(['Travel', 'Meals', 'Office Supplies', 'Equipment', 'Services']),
            'user_id' => User::factory(),
            'company_id' => function (array $attributes) {
                return User::find($attributes['user_id'])->company_id;
            },
        ];
    }

    /**
     * Specify the user and company for the expense.
     */
    public function forUserAndCompany(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);
    }
}
