<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 3 companies with their users and expenses
        Company::factory(3)->create()->each(function ($company) {
            // For each company, create 3-4 users
            User::factory(rand(3, 4))->create([
                'company_id' => $company->id
            ])->each(function ($user) use ($company) {
                // For each user, create 2-3 expenses
                Expense::factory(rand(2, 3))->create([
                    'user_id' => $user->id,
                    'company_id' => $company->id
                ]);
            });
        });

        // Ensure a company exists for the test admin
        $firstCompany = Company::first() ?? Company::factory()->create();

        // Create test admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'company_id' => $firstCompany->id,
            ]
        );
    }
}
