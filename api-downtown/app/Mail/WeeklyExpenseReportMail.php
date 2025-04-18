<?php

namespace App\Mail;

use App\Models\Companies;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $expenses;

    public function __construct(Companies $company, $expenses)
    {
        $this->company = $company;
        $this->expenses = $expenses;
    }

    public function build()
    {
        return $this->subject('Weekly Expense Report - Multi-Tenant SaaS')
                    ->view('emails.weekly_expense_report')
                    ->with([
                        'app_name' => 'Multi-Tenant SaaS',
                        'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),
                    ]);
    }
}