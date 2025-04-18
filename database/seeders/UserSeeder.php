<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company1 = Company::create([
            'name' => 'Test One Company',
            'email' => 'admin@testone.com',
        ]);
        $user = User::create([
            'name' => 'Admin1',
            'email' => 'admin1@example.org',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company1->id,
        ]);


        $company2 = Company::create([
            'name' => 'Test Two Company',
            'email' => 'admin@test2.com',
        ]);
        $user = User::create([
            'name' => 'Admin2',
            'email' => 'admin2@example.org',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company2->id,
        ]);


        $company3 = Company::create([
            'name' => 'Test Three Company',
            'email' => 'admin@test3.com',
        ]);
        $user = User::create([
            'name' => 'Admin3',
            'email' => 'admin3@example.org',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'company_id' => $company3->id,
        ]);

    }
}
