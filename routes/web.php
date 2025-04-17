<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf', function () {
    $startDate = now()->startOfWeek();
    $endDate = now()->endOfWeek();
    $expenses = \App\Models\Expense::whereBetween('created_at', [$startDate, $endDate])
    ->where('company_id', 1)
        ->with(['user'])
        ->get();
    $totalAmount = $expenses->sum('amount');
    $pdf = Pdf::loadView('pdf.weekly-report', [
        'expenses' => $expenses,
        'totalAmount' => $totalAmount,
        'startDate' => $startDate->format('Y-m-d'),
        'endDate' => $endDate->format('Y-m-d'),
    ]);
    return $pdf->download('weekly-report.pdf');
});