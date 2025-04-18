<?php


namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use App\Enums\Roles;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first company (assuming you created at least one in CompanySeeder)
        $company = Company::find(1); // Adjust the ID as necessary

        if ($company) {
            // Create Template users for the first company
            User::create([
                'company_id' => $company->id,
                'name' => 'Admin User',
                'email' => 'admin@acmecorp.com',
                'password' => Hash::make('password123'), // Replace with a secure password
                'role' => Roles::ADMIN, // Use the enum for role
            ]);

            // You can add more users for different roles and companies here
            User::create([
                'company_id' => $company->id,
                'name' => 'Manager User',
                'email' => 'manager@acmecorp.com',
                'password' => Hash::make('password123'),
                'role' => Roles::MANAGER,
            ]);

            User::create([
                'company_id' => $company->id,
                'name' => 'Employee User',
                'email' => 'employee@acmecorp.com',
                'password' => Hash::make('password123'),
                'role' => Roles::EMPLOYEE,
            ]);

        }
        // Create users for the company with ID 2
        $company2 = Company::find(2);
        if ($company2){
            // Create Template users for the second company
            User::create([
                "company_id" => $company2->id,
                "name" => "Admin User 2",
                "email" => "nsikanabasi.idung@gmail.com",
                "password" => Hash::make("password123"), // Replace with a secure password
                "role" => Roles::ADMIN, // Use the enum for role
            ]);

            User::factory()
                ->count(2)
                ->create([
                    "company_id" => $company2->id,
                    "role" => Roles::EMPLOYEE,
                ]);
        }
    }
}