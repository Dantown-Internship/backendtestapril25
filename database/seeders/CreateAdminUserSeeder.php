<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('securePass123'),
            'role' => UserRole::Admin,
        ];

        $company = [
            'name' => 'TechNova Solutions',
            'email' => 'info@technova.com',
        ];

        $company = Company::updateOrCreate(['email' =>  $company['email']], $company);
        $user['company_id'] = $company->id;
        User::updateOrCreate(['email' => $user['email']], $user);
    }
}
