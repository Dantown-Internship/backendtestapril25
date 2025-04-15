<?php

namespace App\Notifications;

use App\Models\Scopes\CompanyScope;
use Illuminate\Support\Collection as ArrCollection;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class WeeklyExpenseReport extends Notification
{
    use Queueable;

    public $meta;

    /**
     * Create a new notification instance.
     */
    public function __construct(object $meta)
    {
        $this->meta = $meta;
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
        $company = $notifiable->company;

        $expenses = $company->expenses()->withoutGlobalScope(CompanyScope::class)->where('user_id', auth()->id())
        ->whereBetween('created_at', [$this->meta->startDate, $this->meta->endDate])->get();
        
        return (new MailMessage)
        ->subject('Weekly Expense Report')
        ->markdown('weekly-expense-report', [
            'startDate' => $this->meta->startDate,
            'endDate' => $this->meta->endDate,
            'expenses' => $expenses,
            'totalExpenses' => $expenses->sum('amount'),
            'notes' => 'No Additional Notes'
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
