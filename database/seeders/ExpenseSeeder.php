<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Expense;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $expenses = [

            // Expenses for Acme Corporation (company_id = 1)
            [
                'company_id' => 1,
                'user_id'    => 1, // Alice Admin
                'title'      => 'Office Supplies',
                'amount'     => 200.50,
                'category'   => 'Supplies',
            ],

            // Expense for Globex Industries (company_id = 2)
            [
                'company_id' => 1,
                'user_id'    => 2, // Mark Manager
                'title'      => 'Team Lunch',
                'amount'     => 150.00,
                'category'   => 'Entertainment',
            ],

            [
                'company_id' => 2,
                'user_id'    => 3, // Eve Employee
                'title'      => 'Taxi Fare',
                'amount'     => 45.75,
                'category'   => 'Travel',
            ]

        ];
        
        foreach($expenses as $expense) {
            Expense::updateOrCreate($expense);
        }
        
    }
}
