<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            Expense::factory()->count(20)->forUser($user)->create();
        });
    }
}