<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    protected $signature = 'ibidapoexpense:test-db';
    protected $description = 'Test database connection and check users table';

    public function handle()
    {
        try {
            // Test connection
            DB::connection()->getPdo();
            $this->info('Database connection successful!');

            // Check users table
            $users = DB::table('users')->get();
            $this->info('Found ' . $users->count() . ' users in the database.');

            foreach ($users as $user) {
                $this->info("User: {$user->email} (Role: {$user->role})");
            }

        } catch (\Exception $e) {
            $this->error('Database connection failed!');
            $this->error($e->getMessage());
        }
    }
}
