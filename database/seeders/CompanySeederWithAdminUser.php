<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeederWithAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::factory()
        ->count(5)
        ->create()
        ->each(function ($company) {
            $company->users()->save(
                User::factory()->create([
                    'company_id' => $company->id,
                    'name' => 'Admin',
                    'email' => 'admin@' . str($company->name)->lower()->value() . '.com',
                    'role' => User::ROLE_ADMIN,
                ])
            );
        });
    }
}
