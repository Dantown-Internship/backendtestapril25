<?php

namespace App\Exports;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct(private Collection $expenses)
    {
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->expenses;
    }

    public function map($expense): array
    {
        return [
            $expense->id,
            $expense->user->name,
            $expense->user->email,
            $expense->title,
            $expense->amount,
            $expense->category,
            $expense->company->name,
            Carbon::parse($expense->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Expense ID',
            'User',
            'Email',
            'Expense Title',
            'Amount',
            'Category',
            'Company',
            'Date',
        ];
    }
}
