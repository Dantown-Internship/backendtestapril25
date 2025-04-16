<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('Enter admin email');
        $password = $this->secret('Enter admin password');
        $company_id = $this->ask('Enter company Id');

        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');
            return 1;
        }

        $admin = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'Admin',
            'company_id' => $company_id
        ]);

        $this->info('Admin user created successfully!');
        $this->line("Email: {$admin->email}");
        return 0;
    }
}
