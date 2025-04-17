<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            // Create a company
            $company = Company::create([
                'name' => "Company $i",
                'email' => "company$i@example.com",
            ]);

            // Create a user with admin role
            User::create([
                'name' => "Admin $i",
                'email' => "admin$i@example.com",
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => User::ROLE_ADMIN,
            ]);
        }
    }
}
