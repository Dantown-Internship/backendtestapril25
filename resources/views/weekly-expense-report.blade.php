<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Expense Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .report-info { margin-bottom: 20px; }
        .expense-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .expense-table th, .expense-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .expense-table th { background-color: #f4f4f4; }
        .total-section { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weekly Expense Report</h1>
            <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
        </div>

        <div class="report-info">
            <p><strong>Report Period:</strong> {{ $startDate ?? 'N/A' }} to {{ $endDate ?? 'N/A' }}</p>
            <p><strong>Company:</strong> {{ $company ?? 'N/A' }}</p>
        </div>

        <table class="expense-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Employee</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses ?? collect([]) as $expense)
                    <tr>
                        <td>{{ $expense->title ?? 'N/A' }}</td>
                        <td>{{ $expense->user->name ?? 'N/A' }}</td>
                        <td>{{ $expense->category ?? 'N/A' }}</td>
                        <td>${{ number_format($expense->amount ?? 0, 2) }}</td>
                        <td>{{ $expense->created_at ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">No expenses for this period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-section">
            <p>Total Expenses: ${{ number_format($totalExpenses ?? 0, 2) }}</p>
        </div>

        <div class="notes">
            <h3>Notes:</h3>
            <p>{{ $notes ?? 'No additional notes' }}</p>
        </div>
    </div>
</body>
</html>