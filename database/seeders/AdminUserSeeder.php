<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create([
            'name' => 'Dantown HR',
            'email' => 'admin@dantownhr.com'
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'System Admin',
            'email' => 'admin@dantownhr.com',
            'password' => Hash::make('admin123'), 
            'role' => 'Admin'
        ]);

        // Optional: Create sample manager and employee
        User::create([
            'company_id' => $company->id,
            'name' => 'Sample Manager',
            'email' => 'manager@dantownhr.com',
            'password' => Hash::make('manager123'),
            'role' => 'Manager'
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'Sample Employee',
            'email' => 'employee@dantownhr.com',
            'password' => Hash::make('employee123'),
            'role' => 'Employee'
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@dantownhr.com');
        $this->command->info('Password: admin123');
    }
}
