<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        \App\Models\Company::create([
            'name' => 'Example Company',
            'email' => 'example@company.com',
        ]);
    }
    
}
