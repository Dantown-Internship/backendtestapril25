<?php

// database/factories/ExpenseFactory.php
namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'company_id' => fn (array $attributes) => User::find($attributes['user_id'])->company_id,
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement(['Travel', 'Meals', 'Entertainment', 'Supplies', 'Equipment']),
            'description' => $this->faker->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}