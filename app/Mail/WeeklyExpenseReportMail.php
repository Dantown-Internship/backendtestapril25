<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;


    public $admin;
    public $expenses;
    protected $pdfData;

    public function __construct($admin, $expenses)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;

        $this->pdfData = Pdf::loadView('reports.weekly_expense', [
            'admin' => $admin,
            'expenses' => $expenses
        ])->output();
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Expense Report Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly',
            with: [
                'admin' => $this->admin,
            ],
        );
    }

     public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfData, 'Weekly_Expense_Report.pdf')
                      ->withMime('application/pdf')
        ];
    }
}
