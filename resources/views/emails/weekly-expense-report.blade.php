<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .expense-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .expense-table th,
        .expense-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .expense-table th {
            background-color: #f8f9fa;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weekly Expense Report</h1>
            <p>Company: {{ $company->name }}</p>
            <p>Period: {{ now()->subWeek()->format('Y-m-d') }} to {{ now()->format('Y-m-d') }}</p>
        </div>

        <table class="expense-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Category</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense->title }}</td>
                        <td>${{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="total">
                        Total: ${{ number_format($total, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
