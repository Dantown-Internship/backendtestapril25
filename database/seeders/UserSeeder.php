<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all seeded companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Create admin user for each company
            User::create([
                'name' => 'Admin User',
                'email' => 'admin_' . $company->id . '@example.com',
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'role' => 'Admin',
            ]);

            // Create manager user for each company
            User::create([
                'name' => 'Manager User',
                'email' => 'manager_' . $company->id . '@example.com',
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'role' => 'Manager',
            ]);

            // Create multiple employee users for each company
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'name' => 'Employee ' . $i,
                    'email' => 'employee' . $i . '_' . $company->id . '@example.com',
                    'password' => Hash::make('password123'),
                    'company_id' => $company->id,
                    'role' => 'Employee',
                ]);
            }
        }
    }
}
