<?php

namespace App\Jobs;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; // 👈 add this
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogExpenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; // 👈 add Dispatchable here

    protected $expense;

    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    public function handle(): void
    {
        Log::info('Expense created', [
            'id' => $this->expense->id,
            'title' => $this->expense->title,
            'amount' => $this->expense->amount,
            'user_id' => $this->expense->user_id ?? null,
            'company_id' => $this->expense->company_id ?? null,
        ]);
    }
}
