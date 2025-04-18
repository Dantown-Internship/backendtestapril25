<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
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
        $timestamp = fake()->dateTimeBetween('-1 week', 'now');
        $company = Company::factory()->create();

        return [
            'company_id' => $company->id,
            'user_id' => User::factory()->create(['company_id' => $company->id])->id,
            'title' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'category' => fake()->randomElement(ExpenseCategory::cases())->value,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}
