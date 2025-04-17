<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companyA = Company::create([
            'name' => 'Company A',
            'email' => 'company_a@example.com',
        ]);

        $companyB = Company::create([
            'name' => 'Company B',
            'email' => 'company_b@example.com',
        ]);

        $adminA = User::create([
            'name' => 'Admin User A',
            'email' => 'admin_a@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyA->id, // Assign to Company A
            'role' => 'Admin',
        ]);

        $managerA = User::create([
            'name' => 'Manager User A',
            'email' => 'manager_a@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyA->id, // Assign to Company A
            'role' => 'Manager',
        ]);

        $employeeA1 = User::create([
            'name' => 'Employee User A',
            'email' => 'employee_a@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyA->id, // Assign to Company A
            'role' => 'Employee',
        ]);

        $employeeA2 = User::create([
            'name' => 'Employee2 User A',
            'email' => 'employee2_a@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyA->id, // Assign to Company A
            'role' => 'Employee',
        ]);


        $adminB = User::create([
            'name' => 'Admin User B',
            'email' => 'admin_b@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyB->id, // Assign to Company B
            'role' => 'Admin',
        ]);

        $managerB = User::create([
            'name' => 'Manager User B',
            'email' => 'manager_b@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyB->id, // Assign to Company B
            'role' => 'Manager',
        ]);

        $employeeB = User::create([
            'name' => 'Employee User B',
            'email' => 'employee_b@example.com',
            'password' => Hash::make('password'),
            'company_id' => $companyB->id, // Assign to Company B
            'role' => 'Employee',
        ]);

        Expense::create([
            'company_id' => $companyA->id,
            'user_id' => $adminA->id,
            'title' => 'Office Supplies',
            'amount' => 50.00,
            'category' => 'Supplies',
        ]);

        Expense::create([
            'company_id' => $companyA->id,
            'user_id' => $managerA->id,
            'title' => 'Travel Expenses',
            'amount' => 200.00,
            'category' => 'Travel',
        ]);

        Expense::create([
            'company_id' => $companyA->id,
            'user_id' => $employeeA1->id,
            'title' => 'Lunch',
            'amount' => 15.50,
            'category' => 'Food',
        ]);
         Expense::create([
            'company_id' => $companyA->id,
            'user_id' => $employeeA2->id,
            'title' => 'Equipment',
            'amount' => 150.00,
            'category' => 'Equipment',
        ]);

        Expense::create([
            'company_id' => $companyB->id,
            'user_id' => $adminB->id,
            'title' => 'Marketing Materials',
            'amount' => 100.00,
            'category' => 'Marketing',
        ]);

        Expense::create([
            'company_id' => $companyB->id,
            'user_id' => $managerB->id,
            'title' => 'Client Meeting',
            'amount' => 75.00,
            'category' => 'Meetings',
        ]);

        Expense::create([
            'company_id' => $companyB->id,
            'user_id' => $employeeB->id,
            'title' => 'Software License',
            'amount' => 300.00,
            'category' => 'Software',
        ]);
    }
}
