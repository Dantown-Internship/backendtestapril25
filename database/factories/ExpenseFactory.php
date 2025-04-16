<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'title' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement(['Travel', 'Meals', 'Office Supplies', 'Entertainment', 'Other']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
