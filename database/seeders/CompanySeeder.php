<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample companies for testing
        Company::create([
            'name' => 'Acme Corporation',
            'email' => 'admin@acmecorp.com',
        ]);

        Company::create([
            'name' => 'Wayne Enterprises',
            'email' => 'admin@wayne.com',
        ]);

        Company::create([
            'name' => 'Stark Industries',
            'email' => 'admin@stark.com',
        ]);
    }
}
