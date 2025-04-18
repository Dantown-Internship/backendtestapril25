<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', User::ROLE_EMPLOYEE)->orWhere('role', User::ROLE_MANAGER)->get();

        $users->each(function($user){
            Expense::factory()->count(3)->create([
                'user_id' => $user->id,
                'company_id' => $user->company_id
            ]);
        });
    }
}
