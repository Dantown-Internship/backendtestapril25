<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyExpenseReport extends Notification implements ShouldQueue
{
    use Queueable;

    private $statistics;
    private $expenses;

    public function __construct($statistics, $expenses)
    {
        $this->statistics = $statistics;
        $this->expenses = $expenses;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Weekly Expense Report - ' . $this->statistics['period']['start'] . ' to ' . $this->statistics['period']['end'])
            ->markdown('emails.expenses.weekly-report', [
                'user' => $notifiable,
                'statistics' => $this->statistics,
                'expenses' => $this->expenses
            ]);
    }
} 