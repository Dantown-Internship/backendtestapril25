<?php

namespace App\Mail;

use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WeeklyExpenseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    private $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;

        try {
            // Generate PDF
            $this->pdfPath = PdfService::generatePdf(
                'pdf.weekly-expense-report',
                $reportData,
                'weekly_expense_report'
            );
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF: '.$e->getMessage());
            $this->pdfPath = null;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $startDate = $this->reportData['startDate']->format('M d');
        $endDate = $this->reportData['endDate']->format('M d, Y');

        return new Envelope(
            subject: "Weekly Expense Report: {$startDate} - {$endDate}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-expenses',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (! $this->pdfPath || ! Storage::disk('local')->exists($this->pdfPath)) {
            Log::error('PDF file not found at path: '.$this->pdfPath);

            return [];
        }

        return [
            Attachment::fromPath(Storage::disk('local')->path($this->pdfPath))
                ->as('weekly_expense_report.pdf')
                ->withMime('application/pdf'),
        ];

    }

    /**
     * Clean up resources after the email is sent
     */
    public function __destruct()
    {
        // Delete the PDF file after the email is sent
        if ($this->pdfPath && Storage::disk('local')->exists($this->pdfPath)) {
            Storage::disk('local')->delete($this->pdfPath);
        }
    }
}
