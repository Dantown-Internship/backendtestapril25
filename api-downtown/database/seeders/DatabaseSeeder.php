<?php

namespace Database\Seeders;

use App\Models\Companies;
use App\Models\Expenses;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $company = Companies::create(['name' => 'Test Co', 'email' => 'test@co.com']);
    $admin = \App\Models\User::create([
        'company_id' => $company->id,
        'name' => 'Admin User',
        'email' => 'admin@co.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'role' => 'Admin'
    ]);
    Expenses::create([
        'company_id' => $company->id,
        'user_id' => $admin->id,
        'title' => 'Travel',
        'amount' => 100.50,
        'category' => 'Business',
        'created_at' => now()->startOfWeek()
    ]);
    }
}
