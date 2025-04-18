<?php

namespace Database\Factories;

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
        $categories = ['Office Supplies', 'Travel', 'Meals', 'Equipment', 'Software', 'Services', 'Other'];
        
        $company = Company::inRandomOrder()->first() ?? Company::factory()->create();
        
        $user = User::where('company_id', $company->id)->inRandomOrder()->first();
        
        if (!$user) {
            $user = User::factory()->create([
                'company_id' => $company->id,
            ]);
        }
        
        return [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'category' => $this->faker->randomElement($categories),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
