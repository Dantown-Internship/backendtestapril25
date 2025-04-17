<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $companies = \App\Models\Company::factory(5)->create();

        foreach ($companies as $company) {
            foreach (Role::cases() as $role) {
                // Create user with the role and associated with the company
                $user = \App\Models\User::factory()->create([
                    'company_id' => $company->id,
                    'role' => $role->value,
                    'name' => $role->name,  // Consider using a more realistic name for users
                    'email' => $role->value.$company->id.'@mail.com',
                    'password' => bcrypt('password'),
                ]);

                // Create expenses for this user
                \App\Models\Expense::factory(5)->create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ]);
            }
        }

    }
}
