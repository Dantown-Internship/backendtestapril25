<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Company::factory(3)->create()->each(function ($company) {
            $admin = User::factory()->create([
                'company_id' => $company->id,
                'role'       => 'Admin',
                'email'      => 'admin@' . strtolower($company->name) . '.com',
            ]);

            $managers = User::factory(2)->create([
                'company_id' => $company->id,
                'role'       => 'Manager',
            ]);

            $employees = User::factory(3)->create([
                'company_id' => $company->id,
                'role'       => 'Employee',
            ]);

            $users = collect([$admin])->merge($managers)->merge($employees);

            $users->each(function ($user) use ($company) {
                Expense::factory(5)->create([
                    'company_id' => $company->id,
                    'user_id'    => $user->id,
                ]);
            });
        });
    }
}
