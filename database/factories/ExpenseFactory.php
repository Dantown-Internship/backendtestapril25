<?php

namespace Database\Factories;

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
            'title' => fake()->sentence(),
            'amount' => fake()->numberBetween(10000, 150000),
            'category' => fake()->randomElement(['Vehicles', 'Office Equipments', 'Food and Beverages', 'Stationaries', 'Healthcare'])
        ];
    }
}
