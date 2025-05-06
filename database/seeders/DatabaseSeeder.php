<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $companies = Company::factory()
            ->count(5)
            ->create();

        foreach ($companies as $company) {
            $admins = User::factory()
                ->count(3)
                ->state(fn () => [
                    'role' => 'Admin',
                    'company_id' => $company->id,
                ])
                ->create();

            $others = User::factory()
                ->count(47)
                ->state(fn () => [
                    'role' => fake()->randomElement(['Manager', 'Employee']),
                    'company_id' => $company->id,
                ])
                ->create();

            $allUsers = $admins->merge($others);

            $allUsers->each(function ($user) {
                Expense::factory()->count(10)->create([
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                ]);
            });
        }

        $this->command->info('Seeding complete!');
    }
}
