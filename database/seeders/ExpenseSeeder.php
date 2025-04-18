<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample expense categories
        $categories = [
            'Travel',
            'Meals',
            'Office Supplies',
            'Technology',
            'Entertainment',
            'Marketing',
            'Training',
            'Miscellaneous'
        ];

        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Create 5-10 random expenses for each user
            $numExpenses = rand(5, 10);

            for ($i = 0; $i < $numExpenses; $i++) {
                // Create a random expense
                $expense = Expense::create([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id,
                    'title' => 'Expense #' . ($i + 1) . ' - ' . $categories[array_rand($categories)],
                    'amount' => rand(10, 1000) + rand(0, 99) / 100,
                    'category' => $categories[array_rand($categories)],
                ]);

                // Create an audit log entry for the expense creation
                AuditLog::create([
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'action' => 'create',
                    'changes' => json_encode([
                        'expense_id' => $expense->id,
                        'old' => null,
                        'new' => $expense->toArray(),
                    ]),
                ]);
            }
        }
    }
}
