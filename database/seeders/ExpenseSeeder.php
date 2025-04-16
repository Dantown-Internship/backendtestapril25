<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder class for populating the expenses table with initial data.
 *
 * This seeder creates random expenses for each employee user in the system.
 * Each employee gets between 5-10 random expenses with different categories.
 */
class ExpenseSeeder extends Seeder
{
    /**
     * List of predefined expense categories.
     *
     * @var array<string>
     */
    private $categories = [
        'Travel',
        'Meals',
        'Office Supplies',
        'Equipment',
        'Training',
        'Entertainment',
        'Other',
    ];

    /**
     * Run the database seeds.
     *
     * For each employee in the system:
     * 1. Generates a random number of expenses (5-10)
     * 2. Creates expenses with random categories
     * 3. Associates expenses with the employee and their company
     *
     * @return void
     */
    public function run(): void
    {
        $employees = User::where('role', 'Employee')->get();

        foreach ($employees as $employee) {
            // Generate a random number of expenses for each employee
            $numberOfExpenses = rand(5, 10);

            // Create the specified number of random expenses
            for ($i = 0; $i < $numberOfExpenses; $i++) {
                Expense::factory()->create([
                    'company_id' => $employee->company_id,
                    'user_id' => $employee->id,
                    'category' => $this->categories[array_rand($this->categories)],
                ]);
            }
        }
    }
}
