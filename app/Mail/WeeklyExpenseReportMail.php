<?php
// app/Mail/WeeklyExpenseReportMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Company;
use Illuminate\Support\Collection;

class WeeklyExpenseReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Company $company;
    public Collection $expenses;

    public function __construct(Company $company, Collection $expenses)
    {
        $this->company = $company;
        $this->expenses = $expenses;
    }

    public function build(): self
    {
        return $this->subject('Weekly Expense Report')
            ->view('emails.weekly-report');
    }
}