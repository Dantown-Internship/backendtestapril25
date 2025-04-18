<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public User $admin;
    public Company $company;
    public Collection $expenses;
    public float $totalAmount;

    public function __construct(User $admin, Company $company, Collection $expenses, float $totalAmount)
    {
        $this->admin = $admin;
        $this->company = $company;
        $this->expenses = $expenses;
        $this->totalAmount = $totalAmount;
    }

    public function build()
    {
        return $this->markdown('emails.expenses.weekly-report')
            ->subject("Weekly Expense Report for {$this->company->name}");
    }
}