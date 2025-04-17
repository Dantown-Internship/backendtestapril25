<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExpenseReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     */
    public function __construct(public Collection $expenses, public $from, public $to) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), 'contact@bowofade.com'),
            subject: "Weekly Expense Report: {$this->from} - {$this->to}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.expense_report',
        );
    }
}