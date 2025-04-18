<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\Role;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $companyCount = Company::count();
        Log::info("Companies before seeding: $companyCount");

        $companies = Company::factory(5)->create();
        Log::info("Companies after creating 5: " . Company::count());

        foreach ($companies as $company) {
            foreach (Role::cases() as $role) {
                Log::info("Creating user for company {$company->id} with role {$role->value}");
                // Create user with the role and associated with the company
                $user = User::factory()->create([
                    'company_id' => $company->id,
                    'role' => $role->value,
                    'name' => $role->name,  // Consider using a more realistic name for users
                    'email' => $role->value.$company->id.'@mail.com',
                    'password' => bcrypt('password'),
                ]);
                Log::info("Companies after creating user: " . Company::count());

                // Create expenses for this user
                Expense::factory(5)->forUser($user)->create();
                Log::info("Companies after creating expenses: " . Company::count());
            }
        }

    }
}
