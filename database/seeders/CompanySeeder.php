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
        $companies = [
            [
                'name'  => 'Acme Corporation',
                'email' => 'info@acme-corp.com',
            ],

            [
                'name'  => 'Globex Industries',
                'email' => 'contact@globex.com',
            ],

        ];
        
        foreach($companies as $data) {

            // Separate lookup key from values
            $lookup = ['email' => $data['email']];

            Company::updateOrCreate($lookup, $data);
        }
    }
}
