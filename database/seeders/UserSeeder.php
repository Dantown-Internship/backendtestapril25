<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [

            // Admin for Acme Corporation
            [
                'name'       => 'Alice Admin',
                'email'      => 'alice@acme-corp.com',
                'password'   => 'password',
                'company_id' => 1,
                'role'       => UserRole::Admin->value,
            ],

            // Manager for Acme Corporation
            [
                'name'       => 'Mark Manager',
                'email'      => 'mark@acme-corp.com',
                'password'   => 'password',
                'company_id' => 1,
                'role'       => UserRole::Manager->value,
            ],

            // Employee for Globex Industries
            [
                'name'       => 'Eve Employee',
                'email'      => 'eve@globex.com',
                'password'   => 'password',
                'company_id' => 2,
                'role'       => UserRole::Employee->value,
            ]
        ];
       
        foreach($users as $data) {

            // Separate lookup key from values
            $lookup = ['email' => $data['email']];

            $data['password'] = Hash::make($data['password']);
            
            User::updateOrCreate($lookup, $data);
        }
    }
}
