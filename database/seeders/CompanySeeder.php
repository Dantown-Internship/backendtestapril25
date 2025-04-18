<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Acme Corp',
            'email' => 'info@acmecorp.com',
        ]);

        Company::create([
            'name' => 'AD-RIAN Studio',
            'email' => 'adriantony247@gmail.com',
        ]);

        Company::create([
            'name' => 'Globex Industries',
            'email' => 'contact@globex.net',
        ]);

        // Add more companies as needed
    }
}