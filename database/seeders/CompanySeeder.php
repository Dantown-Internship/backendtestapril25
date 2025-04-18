<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Test Company',
            'email' => 'testcompany@example.com', // ✅ required column
            // Add other required fields if your schema has more
        ]);
    }
}
