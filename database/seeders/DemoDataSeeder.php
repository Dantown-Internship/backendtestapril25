<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Acme Inc.',
                'email' => 'info@acme.com',
            ],
            [
                'name' => 'Globex Corporation',
                'email' => 'info@globex.com',
            ],
        ];

        foreach ($companies as $companyData) {
            $company = Company::create($companyData);
            
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Admin',
            ]);
            
            $managerUser = User::create([
                'name' => 'Manager User',
                'email' => 'manager@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Manager',
            ]);
            
            $employeeUser = User::create([
                'name' => 'Employee User',
                'email' => 'employee@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'role' => 'Employee',
            ]);
            
            $expenseCategories = ['Office Supplies', 'Travel', 'Meals', 'Equipment', 'Software'];
            
            foreach ([$adminUser, $managerUser, $employeeUser] as $user) {
                for ($i = 0; $i < 5; $i++) {
                    Expense::create([
                        'company_id' => $company->id,
                        'user_id' => $user->id,
                        'title' => 'Expense #' . ($i + 1) . ' by ' . $user->name,
                        'amount' => rand(10, 1000) / 10,
                        'category' => $expenseCategories[array_rand($expenseCategories)],
                    ]);
                }
            }
        }
    }
}
