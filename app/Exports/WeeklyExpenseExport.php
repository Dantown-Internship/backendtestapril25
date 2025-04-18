<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WeeklyExpenseExport implements FromCollection, WithHeadings
{
    protected $expenses;

    public function __construct(Collection $expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses->map(function ($expense) {
            return [
                'Title' => $expense->title,
                'Amount' => $expense->amount,
                'Category' => $expense->category,
                'User' => $expense->user->name,
                'Date' => $expense->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Title', 'Amount', 'Category', 'User', 'Date'];
    }
}
