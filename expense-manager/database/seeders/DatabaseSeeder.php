<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Company::factory()
            ->count(3)
            ->has(
                User::factory()
                    ->count(5)
                    ->state(fn(array $attributes, Company $company) => ['company_id' => $company->id])
                    ->has(
                        Expense::factory()
                            ->count(10)
                            ->state(fn(array $attrs, User $user) => [
                                'company_id' => $user->company_id,
                                'user_id' => $user->id
                            ])
                    )
            )
            ->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
