<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in a specific order to maintain referential integrity
        $this->call([
            CompanySeeder::class,  // First create companies
            UserSeeder::class,     // Then create users (which need companies)
            ExpenseSeeder::class,  // Finally create expenses (which need users and companies)
        ]);
    }
}
