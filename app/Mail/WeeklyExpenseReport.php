<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $admin;
    public $expenses;
    public function __construct($admin, $expenses)
    {
        $this->admin = $admin;
        $this->expenses = $expenses;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.expense-report')
            ->with([
                'admin' => $this->admin,
                'expenses' => $this->expenses
            ]);
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
