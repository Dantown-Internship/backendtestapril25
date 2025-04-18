<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use App\Models\Company;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users except the test user
        $users = User::whereNotNull('company_id')->get();

        foreach ($users as $user) {
            // Create random number of expenses for each user (1-5)
            $count = rand(1, 5);
            
            Expense::factory()
                ->count($count)
                ->create([
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                ]);
        }
    }
}
