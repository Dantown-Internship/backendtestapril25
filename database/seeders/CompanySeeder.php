<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Eden Technologies',
            'email' => 'contact@eden.com',
        ]);

        Company::create([
            'name' => 'SkyCorp Solutions',
            'email' => 'info@skycorp.com',
        ]);
    }
}
