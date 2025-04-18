<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Company::all()->each(function ($company) {
            User::factory()->count(10)->create([
                'company_id' => $company->id,
            ]);
        });
    }
}