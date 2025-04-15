<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Libs\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\User;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $companies = Company::factory()->count(3)->create();

        $companies->each(function ($company) {
            User::factory()
                ->count(3)
                ->state(new Sequence(
                    ['role' => RoleEnum::ADMIN],
                    ['role' => RoleEnum::MANAGER],
                    ['role' => RoleEnum::EMPLOYEE]
                ))
                ->create([
                    'company_id' => $company->id
                ]);
        });
    }
}
