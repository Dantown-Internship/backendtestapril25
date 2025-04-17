<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run()
    {
        // Create 5 companies
        $companies = Company::factory()
            ->count(5)
            ->create();

        // For each company, create users and expenses
        $companies->each(function ($company) {
            // Create 1 admin user
            $admin = User::factory()
                ->admin()
                ->create(['company_id' => $company->id]);

            // Create 2-3 managers per company
            $managers = User::factory()
                ->count($this->faker->numberBetween(2, 3))
                ->manager()
                ->create(['company_id' => $company->id]);

            // Create 5-10 employees per company
            $employees = User::factory()
                ->count($this->faker->numberBetween(5, 10))
                ->employee()
                ->create(['company_id' => $company->id]);

            // Combine all users for expense creation
            $allUsers = collect([$admin])->merge($managers)->merge($employees);

            // Create 20-50 expenses per company
            Expense::factory()
                ->count($this->faker->numberBetween(20, 50))
                ->create([
                    'company_id' => $company->id,
                    'user_id' => function () use ($allUsers) {
                        return $allUsers->random()->id;
                    }
                ]);
        });

        // Create a test company with known credentials
        $testCompany = Company::create([
            'name' => 'Test Company',
            'email' => 'test@example.com',
        ]);

        // Create specific test accounts with known credentials
        $testAdmin = User::create([
            'company_id' => $testCompany->id,
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123'),
            'role' => 'Admin',
        ]);

        $testManager = User::create([
            'company_id' => $testCompany->id,
            'name' => 'Test Manager',
            'email' => 'manager@test.com',
            'password' => bcrypt('manager123'),
            'role' => 'Manager',
        ]);

        $testEmployee = User::create([
            'company_id' => $testCompany->id,
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'password' => bcrypt('employee123'),
            'role' => 'Employee',
        ]);

        // Create test expenses for the test company
        Expense::factory()
            ->count(10)
            ->create([
                'company_id' => $testCompany->id,
                'user_id' => $testAdmin->id,
            ]);

        // Create some expenses for manager and employee
        Expense::factory()
            ->count(5)
            ->create([
                'company_id' => $testCompany->id,
                'user_id' => $testManager->id,
            ]);

        Expense::factory()
            ->count(5)
            ->create([
                'company_id' => $testCompany->id,
                'user_id' => $testEmployee->id,
            ]);
    }
}