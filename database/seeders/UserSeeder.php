<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\Auth\RoleService;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $roleService = new RoleService();

        $company = Company::firstOrCreate([
            'email' => 'contact@dantown.com',
        ], [
            'id' => Str::uuid(),
            'name' => 'Dantown Corp',
        ]);

        
        $adminRole = $roleService->getRoleByName('admin');
        User::factory()->create([
            'name' => 'Jones Calvin',
            'email' => 'admin@dantown.io',
            'company_id' => $company->id,
            'status' => 'active',
            'role_id' => $adminRole->id, 
        ]);

        
        $employeeRole = $roleService->getRoleByName('employee');
        User::factory()->create([
            'name' => 'Eleanor Armstrong',
            'email' => 'eleanor.armstrong@dantown.io',
            'company_id' => $company->id,
            'status' => 'active',
            'role_id' => $employeeRole->id, 
        ]);

       
        $managerRole = $roleService->getRoleByName('manager');
        User::factory()->create([
            'name' => 'Jonathan Stoves',
            'email' => 'jonathanstoves@dantown.io',
            'company_id' => $company->id,
            'status' => 'active',
            'role_id' => $managerRole->id, 
        ]);

        User::factory()->count(3)->create([
            'company_id' => $company->id,
            'status' => 'active',
            'role_id' => $employeeRole->id, 
        ]);

        User::factory()->count(3)->create([
            'company_id' => $company->id,
            'status' => 'locked',
            'role_id' => $employeeRole->id,
        ]);
    }
}
