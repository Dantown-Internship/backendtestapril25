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
    protected $model = Expense::class;

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
            'title' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'category' => fake()->randomElement([
                'Travel',
                'Meals',
                'Accommodation',
                'Software',
                'Training',
                'Marketing',
                'Others',
            ]),
        ];
    }
}
