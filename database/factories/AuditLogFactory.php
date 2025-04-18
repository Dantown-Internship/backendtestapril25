<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'company_id' => Company::factory(),
            'action'     => $this->faker->randomElement(['updated', 'deleted']),
            'changes'    => [
                'old' => ['title' => 'Old Title'],
                'new' => ['title' => 'New Title'],
            ],
        ];
    }
}
