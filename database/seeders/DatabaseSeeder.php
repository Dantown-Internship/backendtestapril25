<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create company
        $company = Company::factory()->create();

        // Create admin for the company
        $plainPassword = 'password';
        $user = User::factory()->create([
            'company_id' => $company->id,
            'password' => Hash::make($plainPassword),
        ]);

        $this->command->info("******** Admin Credentials ********");
        $this->command->info("Email: {$user->email}");
        $this->command->info("Password: {$plainPassword}");
    }
}
