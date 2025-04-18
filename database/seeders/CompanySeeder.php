<?php
// database/seeders/CompanySeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Make sure table is empty first
        DB::table('users')->where('email', 'test@example.com')->delete();
        DB::table('companies')->where('email', 'testcompany@example.com')->delete();
        
        // Create company and store the result
        $company = Company::create([
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
            // Add other required fields here
        ]);
        
        echo "Company created with ID: " . $company->id . "\n";
        
        // Create a user tied to that company
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);
        
        echo "User created with ID: " . $user->id . "\n";
    }
}