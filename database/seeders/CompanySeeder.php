<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        $companies = [
            'InnoGenius Software Ltd',
            'TechNova Solutions',
            'PixelCraft Studios',
            'NextWave Technologies',
            'CloudNest Innovations',
            'BrightPath Systems',
            'QuantumLeap Labs',
            'CodeCrafters Inc.',
            'NexaCore Technologies',
            'BlueOrbit Software'
        ];
        foreach ($companies as $name) {
            Company::create([
                'name' => $name,
                'email' => $faker->unique()->companyEmail,
            ]);
        }
    }
}
