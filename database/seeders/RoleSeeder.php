<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
    public function run(): void
    {
        $roles = ['admin', 'manager', 'employee'];

        foreach ($roles as $role) {
            Roles::firstOrCreate(['name' => $role]);
        };
    }
}
