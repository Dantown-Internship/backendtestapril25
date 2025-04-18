<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Expenses
            'view expenses',
            'create expenses',
            'update expenses',
            'delete expenses',

            // Users
            'view users',
            'create users',
            'update users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view expenses',
            'create expenses',
            'update expenses',
            'view users',
        ]);

        // Employee role
        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view expenses',
            'create expenses',
        ]);
    }
}
