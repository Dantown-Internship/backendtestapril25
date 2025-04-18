<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WeeklyExpenseExport;

class WeeklyExpenseReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $expenses;
    public $companyName;

    public function __construct($expenses, $companyName = null)
    {
        $this->expenses = $expenses;
        $this->companyName = $companyName;
    }

    public function build()
    {
        $fileName = 'weekly-expense-report-' . now()->format('Ymd_His') . '.xlsx';

        return $this->subject('Weekly Expense Report')
                    ->view('emails.weekly-expense-report')->attachData(
                        Excel::raw(new WeeklyExpenseExport($this->expenses), \Maatwebsite\Excel\Excel::XLSX),
                        $fileName,
                        ['mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                    );
    }
}
