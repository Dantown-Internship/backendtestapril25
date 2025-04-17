<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run()
    {
        \App\Models\Expense::create([
            'title' => 'Office Supplies',
            'amount' => 150.00,
            'category' => 'Office',
            'company_id' => 1,
            'user_id' => 1,
        ]);
    }
    
}
