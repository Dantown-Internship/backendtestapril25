<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Company::with(['users' => fn($q) => $q->where('role', 'Admin')])
            ->get()
            ->each(function ($company) {
                $admins = $company->users;
                $expenses = $company->expenses()->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->get();
    
                Log::info('Weekly report generated', [
                    'company_id' => $company->id,
                    'expense_count' => $expenses->count()
                ]);
    
                // ✅ Send email to each admin
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new WeeklyExpenseReport($company, $expenses));
                        Log::info("Email sent successfully to: " . $admin->email);
                    } catch (\Exception $e) {
                        Log::error("Failed to send email to: " . $admin->email, [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                
            });
    }
}