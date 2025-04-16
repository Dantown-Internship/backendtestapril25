<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Expense;
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
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $expense = Expense::factory()->create([
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        $actions = ['create', 'update', 'delete'];
        $action = $this->faker->randomElement($actions);

        $changes = [];
        if ($action === 'update') {
            $changes = [
                'old' => [
                    'title' => $this->faker->sentence(3),
                    'amount' => $this->faker->randomFloat(2, 10, 1000),
                ],
                'new' => [
                    'title' => $this->faker->sentence(3),
                    'amount' => $this->faker->randomFloat(2, 10, 1000),
                ],
            ];
        }

        return [
            'user_id' => $user->id,
            'company_id' => $company->id,
            'expense_id' => $expense->id,
            'action' => $action,
            'changes' => $changes,
        ];
    }
}
