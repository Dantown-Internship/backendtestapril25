<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'company_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Manager',
            'company_id' => 1,
        ]);

        User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'Employee',
            'company_id' => 1,
        ]);
    }
}
