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
    public function run()
    {
        Company::factory()->create([
            'id' => 1,
            'name' => 'Acme Inc',
            'email' => 'contact@gmail.com',
        ]);

        Company::factory()->create([
            'id' => 2,
            'name' => 'Globex Corp',
            'email' => 'hello@gmail.com',
        ]);
    }
}
