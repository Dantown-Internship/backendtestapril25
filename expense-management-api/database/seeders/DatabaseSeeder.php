<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // Create 5 companies
        Company::factory(5)->create()->each(function ($company) {
            // Create 5 users for each company
            $users = User::factory(5)->make(['company_id' => $company->id]);

            // Assign roles: 1 Admin, 1 Manager, and the rest Employees
            $users[0]->role = 'Admin';
            $users[1]->role = 'Manager';
            foreach ($users->slice(2) as $user) {
                $user->role = 'Employee';
            }

            // Save users to the database
            $users->each(function ($user) use ($company) {
                $user->save();

                // Create 10 expenses for each user
                Expense::factory(10)->create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            });
        });
    }
}
