<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expenses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExpensesSeeder extends Seeder
{
    public function run(): void
    {
       
        for ($i = 1; $i <= 50; $i++) {
            Expenses::create([
                'company_id' => 1, 
                'user_id' => 1, 
                'title' => 'Expense ' . $i,
                'amount' => rand(100, 1000),
                'category' => ['Travel', 'Food', 'Office', 'Utilities'][rand(0, 3)],
            ]);
        }
    }
}
