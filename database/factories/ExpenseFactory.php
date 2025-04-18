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
        return [
            'company_id' => null, // Don't create a company by default
            'user_id' => null,    // Don't create a user by default
            'title' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'category' => fake()->randomElement(ExpenseCategory::cases())->value,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    // Add state methods if needed
    public function forCompany(int $companyId = null)
    {
        return $this->state(function (array $attributes) use ($companyId) {
            $companyId = $companyId ?? Company::factory()->create();
            return [
                'company_id' => $companyId,
            ];
        });
    }

    public function forUser(User $user = null)
    {
        return $this->state(function (array $attributes) use ($user) {
            if ($user) {
                return [
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                ];
            }

            $company = Company::factory()->create();
            $user = User::factory()->create(['company_id' => $company->id]);

            return [
                'user_id' => $user->id,
                'company_id' => $company->id,
            ];
        });
    }
}
