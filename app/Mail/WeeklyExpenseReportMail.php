<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WeeklyExpenseReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $expenses;
    public $admin;

    public function __construct($expenses, $admin)
    {
        $this->expenses = $expenses;
        $this->admin = $admin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Expense Report'
        );
    }

    public function content(): Content
{
    return new Content(
        markdown: 'emails.reports.weekly-expense',
        with: [
            'expenses' => $this->expenses,
            'admin' => $this->admin,
            'total' => $this->expenses->sum('amount'),
        ]
    );
}

    public function attachments(): array
    {
        return [];
    }
}
