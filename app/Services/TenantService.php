<?php
// app/Services/TenantService.php
// app/Services/TenantService.php
namespace App\Services;

use App\Models\Central\Tenant;
use App\Models\Central\User as CentralUser;
use App\Models\Tenant\User as TenantUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class TenantService
{
    public function createTenantWithAdmin(array $data)
    {
        try {
            // 1. Create tenant in central DB
            $tenant = Tenant::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'database_name' => 'tenant_' . uniqid(),
                'subdomain' => $data['subdomain'] ?? null,
            ]);

            // 2. Create tenant database
            DB::statement("CREATE DATABASE {$tenant->database_name}");

            // 3. Run tenant migrations (this should be outside transaction!)
            $this->runTenantMigrations($tenant->database_name);

            // 4. Switch to tenant DB
            $this->setTenantConnection($tenant);

            // 5. Wrap only central and tenant user creation in transaction
            DB::beginTransaction();

            // Central user
            $centralUser = CentralUser::firstOrCreate(
                ['email' => $data['email']],
                ['password' => Hash::make('password')]
            );

            // Add tenant ID if not already present
            $tenantIds = $centralUser->tenant_ids ?? [];
            if (!in_array($tenant->id, $tenantIds)) {
                $tenantIds[] = $tenant->id;
                $centralUser->update(['tenant_ids' => $tenantIds]);
            }

            // Tenant-side admin user
            $tenantUser = TenantUser::create([
                'central_user_id' => $centralUser->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => 'Admin',
                'password' => Hash::make('password'),
            ]);

            // Token for API use
            $token = $centralUser->createToken('api', ['tenant_id' => $tenant->id])->plainTextToken;

            DB::commit();

            return [
                'tenant' => $tenant,
                'tenant_user' => $tenantUser,
                'token' => $token,
            ];
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw new \Exception("An error occurred while creating the tenant.", 0, $e);
        }
    }

    protected function runTenantMigrations($database)
    {
        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $database,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ]]);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'tenant/migrations',
            '--force' => true,
        ]);
    }

    public function setTenantConnection(Tenant $tenant)
    {
        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => $tenant->database_name,
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ]]);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    public function getTenantByIdOrSubdomain($identifier)
    {
        return Tenant::where('id', $identifier)
            ->orWhere('subdomain', $identifier)
            ->firstOrFail();
    }
}
