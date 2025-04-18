<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AuditLog;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $auditLogs = [
            [
                'user_id'    => 1,
                'company_id' => 1,
                'action'     => 'create_expense',
                'changes'    => json_encode([
                    'new' => [
                        'title'    => 'Office Supplies',
                        'amount'   => 200.50,
                        'category' => 'Supplies',
                    ]
                ]),
            ],

            [
                'user_id'    => 2,
                'company_id' => 1,
                'action'     => 'create_expense',
                'changes'    => json_encode([
                    'new' => [
                        'title'    => 'Team Lunch',
                        'amount'   => 150.00,
                        'category' => 'Entertainment',
                    ]
                ]),
            ],

        ];
        
        foreach($auditLogs as $auditLog) {
            AuditLog::updateOrCreate($auditLog);
        }
    }
}
