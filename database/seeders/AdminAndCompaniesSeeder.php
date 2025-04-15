<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AdminAndCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        // default users for testing I omitted register api
        $companies = Company::factory()->count(2)->create();

        foreach ($companies as $key => $company) {
            $company->users()->create([
                'name' => fake()->name(),
                'email' => 'admin@company1.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
            ]);
        }
    }
}
