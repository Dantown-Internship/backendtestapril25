<?php
// database/seeders/CompanySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Create company and store the result
        $company = Company::create([
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
            // Add other required fields if your schema has more
        ]);

        // Create a user tied to that company
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id, 
            'role' => 'Admin', // if role is required
        ]);
    }
}