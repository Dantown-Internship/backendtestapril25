<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            Expense::factory()->count(10)->create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
            ]);
        });
    }
}
