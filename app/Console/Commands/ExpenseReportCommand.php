<?php

namespace App\Console\Commands;

use App\Jobs\CacCertificateJob;
use App\Jobs\ExpenseReportJob;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpenseReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expenses-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to send weekly expenses report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Sending weekly expense reports...');

        $admins = User::query()->where('role', 'Admin')->get();

        if(is_null($admins))
        {
            Log::info('no admin found');
        }

        foreach ($admins as $admin) {
            $expenses = Expense::query()->where('company_id', $admin->company_id)->latest()->get();

            dispatch(new ExpenseReportJob($admin, $expenses));
        }

        Log::info('Expense reports queued for all admins.');
    }
}
