<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company1 = Company::first();
        $company2 = Company::skip(1)->first();

        // Eden Technologies users
        User::create([
            'name' => 'Alice Admin',
            'email' => 'admin@eden.com',
            'password' => Hash::make('password'),
            'company_id' => $company1->id,
            'role' => 'Admin',
        ]);

        User::create([
            'name' => 'Mark Manager',
            'email' => 'manager@eden.com',
            'password' => Hash::make('password'),
            'company_id' => $company1->id,
            'role' => 'Manager',
        ]);

        User::create([
            'name' => 'Eve Employee',
            'email' => 'employee@eden.com',
            'password' => Hash::make('password'),
            'company_id' => $company1->id,
            'role' => 'Employee',
        ]);

        // SkyCorp users
        User::create([
            'name' => 'Sam Admin',
            'email' => 'admin@skycorp.com',
            'password' => Hash::make('password'),
            'company_id' => $company2->id,
            'role' => 'Admin',
        ]);
    }
}
