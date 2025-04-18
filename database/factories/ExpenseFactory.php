<?php

namespace Database\Factories;

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
        $user = User::factory()->create();

        return [
            'company_id' => $user->company_id,
            'user_id'    => $user->id,
            'title'      => fake()->sentence(3),
            'amount'     => fake()->randomFloat(2, 1, 1000),
            'category'   => fake()->randomElement(['travel', 'office', 'utilities']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
