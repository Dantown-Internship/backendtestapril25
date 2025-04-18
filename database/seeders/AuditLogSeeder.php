<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Expense;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $companyId = $user->company_id;

            AuditLog::factory()->count(5)->create([
                'user_id' => $user->id,
                'company_id' => $companyId,
                'changes' => json_encode([
                    'old' => ['amount' => rand(100, 500)],
                    'new' => ['amount' => rand(600, 1000)],
                ]),
            ]);
        }
    }
}

