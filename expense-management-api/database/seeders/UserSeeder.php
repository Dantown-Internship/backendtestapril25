<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Company::all()->each(function ($company) {
            User::factory()->create([
                'company_id' => $company->id,
                'role' => 'Admin',
                'email' => 'admin@' . strtolower($company->name) . '.com',
                'password' => bcrypt('password'),
            ]);

            User::factory()->count(2)->create([
                'company_id' => $company->id,
                'role' => 'Manager',
            ]);

            User::factory()->count(5)->create([
                'company_id' => $company->id,
                'role' => 'Employee',
            ]);
        });
    }
}
