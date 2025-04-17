<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Company;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();
        \App\Models\User::factory(20)->create([
            'role' => Role::Admin->value,
            'company_id' => $company->id,
        ]);

    }
}
