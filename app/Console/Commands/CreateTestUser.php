<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'ibidapoexpense:create-test-user';
    protected $description = 'Create a test company and admin user';

    public function handle()
    {
        // Create test company
        $company = Company::create([
            'name' => 'Test Company',
            'email' => 'test@company.com',
        ]);

        // Create admin user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'Admin',
        ]);

        $this->info('Test company and admin user created successfully!');
        $this->info('Email: admin@test.com');
        $this->info('Password: password123');
    }
}
