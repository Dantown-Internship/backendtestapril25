<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ManagerAndEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(5)->state(new Sequence(fn($sequence) =>[
            'company_id' => $sequence->index + 1
        ]))->create([
            'role' => User::ROLE_MANAGER
        ]);

        User::factory()->count(5)->state(new Sequence(fn($sequence) =>[
            'company_id' => $sequence->index + 1
        ]))->create([
            'role' => User::ROLE_EMPLOYEE
        ]);
    }
}
