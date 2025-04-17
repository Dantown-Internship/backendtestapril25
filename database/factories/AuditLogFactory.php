<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuditLog::class;

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
            'action' => $this->faker->randomElement(['create', 'update', 'delete']),
            'changes' => [
                'old' => ['field' => 'old_value'],
                'new' => ['field' => 'new_value'],
            ],
            'model_type' => $this->faker->randomElement(['App\\Models\\Expense', 'App\\Models\\User']),
            'model_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
