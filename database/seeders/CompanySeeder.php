<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::factory()->create([
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.com',
        ]);

        Company::factory()->create([
            'name' => 'Tech Solutions Inc',
            'email' => 'info@techsolutions.com',
        ]);

        Company::factory()->create([
            'name' => 'Global Industries',
            'email' => 'support@globalindustries.com',
        ]);
    }
}
