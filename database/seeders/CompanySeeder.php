<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanySeeder extends Seeder
{
    protected $model = Company::class;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // default users for testing I omitted register api
        $companies = Company::factory()->count(2)->create();

        foreach ($companies as $key => $company) {
            $company->users()->create([
                'name' => fake()->name(),
                'email' => "admin@company" . ($key + 1) . ".com",
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'company_id' => $company->id, // Ensure company_id is set
            ]);
        }
    }
}