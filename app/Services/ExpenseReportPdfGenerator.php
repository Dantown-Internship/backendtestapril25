<?php

namespace App\Services;

use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ExpenseReportPdfGenerator
{
    public function generate(Collection $admins, Carbon $startDate, Carbon $endDate)
    {
        $admins->each(function ($admin) use ($startDate, $endDate) {
            $weeklyExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])
                ->where('company_id', $admin->company->id)
                ->with(['user'])
                ->get();
            $totalAmount = $weeklyExpenses->sum('amount');
            $companyName = str($admin->company->name)->title()->value();
            $formatedStartDate = $startDate->format('d/m/Y');
            $formatedEndDate = $endDate->format('d/m/Y');
            $title = "{$companyName} Weekly Report ({$formatedStartDate} -  {$formatedEndDate})";
            $reportStoragePath = $this->getReportStoragePath($companyName);

            $pdf = Pdf::loadView('pdf.expense-report', [
                'title' => $title,
                'weeklyExpenses' => $weeklyExpenses,
                'totalAmount' => $totalAmount,
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d'),
            ]);
            $pdf->save($reportStoragePath);
        });
    }

    public function getReportStoragePath($companyName)
    {
        return storage_path(str("app/tmp/{$companyName}-weekly-report.pdf")->lower()->value());
    }
}