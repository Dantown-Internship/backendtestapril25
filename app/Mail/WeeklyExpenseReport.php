<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $reportData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Expense Report - ' . $this->reportData['company'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expense-report',
            with: [
                'expenses' => $this->reportData['expenses'],
                'total' => $this->reportData['total'],
                'timeframe' => $this->reportData['timeframe'],
                'company' => $this->reportData['company']
            ]
        );
    }
}