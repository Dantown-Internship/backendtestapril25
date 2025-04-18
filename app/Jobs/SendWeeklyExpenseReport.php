<?php
// app/Jobs/SendWeeklyExpenseReport.php

namespace App\Jobs;

use App\Models\Company;
use App\Mail\WeeklyExpenseReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
{
    Log::info('📤 SendWeeklyExpenseReport job started');

    $startDate = now()->subDays(7);
    $endDate = now();

    // Use the imported Company model instead of the fully qualified name
    Company::with(['users' => fn($q) => $q->where('role', 'Admin')])
        ->chunk(50, function ($companies) use ($startDate, $endDate) {
            foreach ($companies as $company) {
                Log::info("🔍 Processing company: {$company->name}");

                $expenses = $company->expenses()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                foreach ($company->users as $admin) {
                    Log::info("📧 Sending report to admin: {$admin->email} with " . $expenses->count() . " expenses");
                    Mail::to($admin->email)->send(new WeeklyExpenseReportMail($company, $expenses));
                }
                
                // Always send a copy to your personal email
                Mail::to('samuelayo61@gmail.com')->send(new WeeklyExpenseReportMail($company, $expenses));
            }
        });

    Log::info('✅ SendWeeklyExpenseReport job finished');
}
}