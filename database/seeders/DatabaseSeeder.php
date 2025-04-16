<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main database seeder class that coordinates the execution of all seeders.
 *
 * This seeder ensures that the seeders are run in the correct order to maintain
 * proper data relationships:
 * 1. Companies are created first
 * 2. Users are created for each company
 * 3. Expenses are created for each employee
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Calls the seeders in the following order:
     * 1. CompanySeeder - Creates the initial companies
     * 2. UserSeeder - Creates users for each company
     * 3. ExpenseSeeder - Creates expenses for each employee
     * 4. AuditLogSeeder - Creates audit logs for each company and user
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            UserSeeder::class,
            ExpenseSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
