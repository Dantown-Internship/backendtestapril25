<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        $company = Company::inRandomOrder()->first() ?? Company::factory()->create();
        $user = User::where('company_id', $company->id)->inRandomOrder()->first()
            ?? User::factory()->create(['company_id' => $company->id]);

        return [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'title' => $this->faker->sentence,
            'amount' => $this->faker->randomFloat(2, 50, 5000),
            'category' => $this->faker->randomElement(['Travel', 'Office Supplies', 'Meals', 'Utilities']),
        ];
    }
}
