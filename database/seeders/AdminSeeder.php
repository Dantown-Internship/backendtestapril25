<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::create([
            'name' => 'Company_Expense_Experts',
            'email' => 'superadmin@gamil.com',
        ]);
    
        User::create([
            'company_id' => $company->id,
            'name' => 'SuperAdmin',
            'email' => 'superadmin@gamil.com',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',  // Setting this user as Admin
        ]);
    }
}
