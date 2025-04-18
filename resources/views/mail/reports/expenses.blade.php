@extends('mail.layout')

@section('content')

<h1>Weekly Expenses Report</h1>
<p>Dear {{ $expensesData['admin'] }},</p>
<p>Here is the summary of expenses for {{ $expensesData['company'] }} for the past week:</p>

<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Title</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Category</th>
            <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expensesData['expenses'] as $expense)
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $expense->created_at->format('d-m-Y') }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $expense->title }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $expense->category }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ number_format($expense->amount, 2) }}</td>
        </tr>
        @endforeach
        @if (count($expensesData['expenses']) > 0)
        <tr>
            <td colspan="3" style="border: 1px solid #ddd; padding: 8px;"></td>
            <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{{ number_format($expensesData['total'], 2) }}</td>
        </tr>
        @endif
    </tbody>
</table>

<p>Thank you for your attention.</p>
<p>Best Regards,<br>MultiTenant Team</p>
@endsection