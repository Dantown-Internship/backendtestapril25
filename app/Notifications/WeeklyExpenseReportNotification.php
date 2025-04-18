<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyExpenseReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public float $totalExpense,
        public string $companyName,
        public array $sortedExpenses,
        public array $topSpenders,
        public Carbon $startDate,
        public Carbon $endDate,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("{$this->companyName}'s Weekly Expense Report")
            ->markdown('emails.weekly-report', [
                'user' => $notifiable,
                'companyName' => $this->companyName,
                'totalExpense' => $this->totalExpense,
                'sortedExpenses' => $this->sortedExpenses,
                'topSpenders' => $this->topSpenders,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
