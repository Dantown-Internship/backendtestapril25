<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Expense;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Expense::factory()->count(10)->create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ]);
        }
    }
}

