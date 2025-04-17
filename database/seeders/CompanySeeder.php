<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => 'Acme Corp', 'email' => 'support@acmecorps.uk'],
            ['name' => 'Globex Inc', 'email' => 'hrsupport@globex.co'],
            ['name' => 'Stark Industries',  'email' => 'care@stark.health'],
        ];

        foreach ($companies as $company) {
            Company::create([
                'id'    => Str::uuid(),
                'name' => $company['name'],
                'email' => $company['email']
            ]);
        }
    }
}
