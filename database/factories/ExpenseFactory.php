<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
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
        $categories = [
            'Travel',
            'Meals',
            'Office Supplies',
            'Equipment',
            'Training',
            'Entertainment',
            'Other',
        ];

        return [
            'title' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement($categories),
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
        ];
    }
}
