<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $expenses;

    /**
     * Create a new message instance.
     */
    public function __construct($admin, $expenses)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;
    }

    public function build()
    {
        // Generate PDF from view
        $pdf = Pdf::loadView('reports.weekly', [
            'admin' => $this->admin,
            'expenses' => $this->expenses
        ]);

        return $this->markdown('emails.expense.report')
            ->subject('Weekly Expense Report')
            ->with([
                'admin' => $this->admin,
                'expenses' => $this->expenses
            ])
            ->attachData($pdf->output(), 'Weekly_Expense_Report.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
    /**
     * Get the message envelope.
     */
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
            markdown: 'emails.expense.report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
