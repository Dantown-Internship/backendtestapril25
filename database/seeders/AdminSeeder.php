<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::create([
            'name'  => 'Cybrox Labs.',
            'email' => 'services@cybroxlabs.com',
        ]);

        User::create([
            'name'       => 'Super Admin',
            'email'      => 'admin@cybroxlabs.com',
            'password'   => bcrypt('password'),
            'role'       => 'Admin',
            'company_id' => $company->id,
        ]);
    }
}
