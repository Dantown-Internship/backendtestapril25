<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public $expenses
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Expense Report - ' . $this->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-expense-report',
            with: [
                'company' => $this->company,
                'expenses' => $this->expenses,
                'totalAmount' => $this->expenses->sum('amount'),
            ],
        );
    }
} 