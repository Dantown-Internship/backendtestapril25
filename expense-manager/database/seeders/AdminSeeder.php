<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=> 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), 
            'company_id' => 1, 
            'role' => 'Admin',
        ]);
    }
}
