<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
