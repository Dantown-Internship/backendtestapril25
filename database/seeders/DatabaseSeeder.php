<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     public function run()
//     {
//         $company = Company::create(['name' => 'Test Corp', 'email' => 'test@corp.com']);
//         $admin = User::create([
//             'name' => 'Admin',
//             'email' => 'admin@corp.com',
//             'password' => bcrypt('password'),
//             'company_id' => $company->id,
//             'role' => 'Admin',
//         ]);
//         Expense::create([
//             'company_id' => $company->id,
//             'user_id' => $admin->id,
//             'title' => 'Office Supplies',
//             'amount' => 100.50,
//             'category' => 'Office',
//         ]);
//     }
// }

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // First Company
        $company1 = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $admin1 = User::create([
            'name' => 'Admin One',
            'email' => 'admin1@corp.com',
            'password' => bcrypt('password'),
            'company_id' => $company1->id,
            'role' => 'Admin',
        ]);

        Expense::create([
            'company_id' => $company1->id,
            'user_id' => $admin1->id,
            'title' => 'Office Supplies',
            'amount' => 100.50,
            'category' => 'Office',
        ]);

        // Second Company
        $company2 = Company::create([
            'name' => 'Example Inc',
            'email' => 'info@example.com',
        ]);

        $admin2 = User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => bcrypt('password'),
            'company_id' => $company2->id,
            'role' => 'Admin',
        ]);

        Expense::create([
            'company_id' => $company2->id,
            'user_id' => $admin2->id,
            'title' => 'Travel Expenses',
            'amount' => 250.75,
            'category' => 'Travel',
        ]);
    }
}
