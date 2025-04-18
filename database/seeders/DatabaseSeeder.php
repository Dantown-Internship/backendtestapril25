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
        $companies = Company::factory(5)->create();

        foreach ($companies as $company) {
            foreach (Role::cases() as $role) {
                // Create user with the role and associated with the company
                $user = User::factory()->create([
                    'company_id' => $company->id,
                    'role' => $role->value,
                    'email' => $role->value.$company->id.'@mail.com',
                    'password' => bcrypt('password'),
                ]);

                // Create expenses for this user
                Expense::factory(5)->forUser($user)->create();
            }
        }

    }
}
