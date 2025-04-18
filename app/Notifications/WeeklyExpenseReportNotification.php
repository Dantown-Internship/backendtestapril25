<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyExpenseReportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Expense $userExpenses,
        public float $totalAmount,
        public float $categoryTotals,
        public Carbon $lastWeekStart,
        public Carbon $lastWeekEnd,
    )
    {
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
            ->subject('Weekly Expense Report')
            ->view('emails.weekly-expense-report', [
                'user' => $notifiable,
                'expenses' => $this->userExpenses,
                'totalAmount' => $this->totalAmount,
                'categoryTotals' => $this->categoryTotals,
                'startDate' => $this->lastWeekStart,
                'endDate' => $this->lastWeekEnd,
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
