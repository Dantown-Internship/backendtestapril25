<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Get all users who have the 'Employee' role
        $employees = User::where('role', 'Employee')->get();

        foreach ($employees as $employee) {
            // Get a random category from the employee's company
            $category = Category::where('company_id', $employee->company_id)->inRandomOrder()->first();

            
            // This create expense if a category exists
            if ($category) {
                Expense::create([
                    'title' => $faker->sentence(3),
                    'amount' => $faker->numberBetween(5000, 100000),
                    'user_id' => $employee->id,
                    'category_id' => $category->id,
                    'company_id' => $employee->company_id,
                ]);
            }
        }
    }
}