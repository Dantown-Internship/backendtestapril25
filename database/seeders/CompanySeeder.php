<?php
// database/seeders/CompanySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Test Company',
            'email' => 'testcompany@example.com', // ✅ required column
            // Add other required fields if your schema has more
        ]);
        
        // Create a user tied to that company
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id, // ✅ fix
            'role' => 'Admin', // if role is required
        ]);
    }
}
