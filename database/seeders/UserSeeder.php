<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $roles = ['Admin', 'Manager', 'Employee'];

        $companies = Company::all();

        foreach ($companies as $company) {
            // Let's create 3 users per company
            for ($i = 0; $i < 3; $i++) {
                User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password'),
                    'role' => $roles[array_rand($roles)],
                    'company_id' => $company->id,
                ]);
            }
        }
    }
}