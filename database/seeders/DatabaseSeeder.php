<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CompanySeederWithAdminUser::class,
            ManagerAndEmployeeSeeder::class,
            ExpenseSeeder::class
        ]); 
    }
}
