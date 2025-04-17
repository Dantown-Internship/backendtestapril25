<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Jobs\SendWeeklyExpenseReport;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $companies = Company::with([
            'users' => fn($query) => $query->where('role', RoleEnum::ADMIN),
            'expenses' => fn($query) => $query->where('created_at', '>=', now()->subWeek())
        ])->get();

        foreach ($companies as $company) {
            $admins = $company->users;
            $expenses = $company->expenses;
            foreach ($admins as $admin) {
                SendWeeklyExpenseReport::dispatch($admin, $expenses);
            }
        }

    }
}
