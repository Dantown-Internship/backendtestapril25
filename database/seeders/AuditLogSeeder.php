<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder class for populating the audit_logs table with initial data.
 *
 * This seeder creates sample audit logs for testing and development purposes.
 */
class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates sample audit logs for each company and user combination.
     *
     * @return void
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $users = User::where('company_id', $company->id)->get();

            foreach ($users as $user) {
                // Create 5-10 random audit logs for each user
                $numberOfLogs = rand(5, 10);

                for ($i = 0; $i < $numberOfLogs; $i++) {
                    AuditLog::factory()->create([
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                    ]);
                }
            }
        }
    }
}
