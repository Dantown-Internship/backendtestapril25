<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; }
        .table th { background-color: #f5f5f5; }
        .total { text-align: right; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weekly Expense Report</h1>
            <p>For the week of {{ now()->subWeek()->startOfWeek()->format('M d, Y') }} to {{ now()->subWeek()->endOfWeek()->format('M d, Y') }}</p>
        </div>

        <h2>{{ $company->name }}</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Submitted By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->created_at->format('M d, Y') }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>${{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->user->name }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No expenses recorded for this week.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td class="total">Total:</td>
                    <td class="total">${{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html> 