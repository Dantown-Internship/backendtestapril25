<?php

// database/factories/AuditLogFactory.php
namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'company_id' => fn (array $attributes) => User::find($attributes['user_id'])->company_id,
            'action' => $this->faker->randomElement(['create', 'update', 'delete']),
            'model_type' => $this->faker->randomElement(['App\Models\User', 'App\Models\Expense']),
            'model_id' => $this->faker->numberBetween(1, 100),
            'changes' => json_encode([
                'field' => $this->faker->word(),
                'old' => $this->faker->word(),
                'new' => $this->faker->word(),
            ]),
            'ip_address' => $this->faker->ipv4(),
        ];
    }
}