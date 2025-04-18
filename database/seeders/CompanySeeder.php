<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 companies
        Company::factory()->count(5)->create();
        
        // Create a specific company for testing
        Company::create([
            'name' => 'Acme Corp',
            'email' => 'info@acme.com',
        ]);
    }
}
