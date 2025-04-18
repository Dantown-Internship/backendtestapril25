<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TenantService;

class CreateTenant extends Command
{
    protected $signature = 'tenant:create {name} {email}';
    protected $description = 'Create a new tenant and its database';

    public function handle(TenantService $tenantService)
    {
        $name = $this->argument('name');
        $email = $this->argument('email');

        $tenant = $tenantService->createTenant([
            'name' => $name,
            'email' => $email,
        ]);

        $this->info("Tenant '{$tenant->name}' created with DB: {$tenant->database_name}");
    }
}
