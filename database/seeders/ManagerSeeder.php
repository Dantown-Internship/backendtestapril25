<?php

namespace Database\Seeders;

use App\Constants\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'manager@test.com',
            'role' => UserRole::MANAGER,
        ]);
    }
}
