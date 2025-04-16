<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder class for populating the users table with initial data.
 *
 * This seeder creates users for each company with different roles:
 * - 1 Admin user
 * - 1 Manager user
 * - 3 Employee users
 *
 * All users are created with the default password 'password'.
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * For each company in the database:
     * 1. Creates an admin user with company-specific email
     * 2. Creates a manager user with company-specific email
     * 3. Creates three employee users with company-specific emails
     *
     * @return void
     */
    public function run(): void
    {
        // Create system user for CLI operations
        User::create([
            'name' => 'System User',
            'email' => 'system@ibidapoexpense.com',
            'password' => Hash::make('system-password-' . time()),
            'company_id' => 1, // Assuming company with ID 1 exists
            'role' => 'Admin',
        ]);

        // Create test users for each company
        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            // Create an admin user
            User::create([
                'name' => "Admin User {$company->id}",
                'email' => "admin{$company->id}@example.com",
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Admin',
            ]);

            // Create a manager user
            User::create([
                'name' => "Manager User {$company->id}",
                'email' => "manager{$company->id}@example.com",
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Manager',
            ]);

            // Create an employee user
            User::create([
                'name' => "Employee User {$company->id}",
                'email' => "employee{$company->id}@example.com",
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Employee',
            ]);
        }
    }
}
