<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Jobs\SendExpenseReportJob;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;

class SendWeeklyExpenseReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-expense-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to send weekly expense reports to all company admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $adminUserIds = User::where('role', RoleEnum::ADMIN->value)->pluck('id')->toArray();
        dispatch(new SendExpenseReportJob($adminUserIds));
    }
}
