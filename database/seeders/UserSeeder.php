<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Create an admin for each company
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin' . $company->id . '@example.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Admin',
            ]);

            // Create managers for each company
            User::factory()
                ->count(2)
                ->create([
                    'company_id' => $company->id,
                    'role' => 'Manager',
                ]);

            // Create employees for each company
            User::factory()
                ->count(5)
                ->create([
                    'company_id' => $company->id,
                    'role' => 'Employee',
                ]);
        }
        
        // Make sure we have at least one company
        if ($companies->isNotEmpty()) {
            // Create a test user associated with the first company
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'company_id' => $companies->first()->id,
                'role' => 'Admin',
            ]);
        }
    }
}
