<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Company;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'action' => $this->faker->randomElement(['update', 'delete']),
            'changes' => json_encode([
                'old' => ['amount' => 200],
                'new' => ['amount' => 250],
            ]),
        ];
    }
}

