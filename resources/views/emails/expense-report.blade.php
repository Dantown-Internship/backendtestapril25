<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background: #f5f5f5;
            padding: 15px;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
        }
        .summary {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Weekly Expense Report</h2>
            <p>Company: {{ $company->name }}</p>
            <p>Period: {{ $startDate }} to {{ $endDate }}</p>
        </div>
        
        <div class="summary">
            <h3>Summary</h3>
            <p>Total Expenses: {{ $expenseCount }}</p>
            <p>Total Amount: ${{ number_format($totalAmount, 2) }}</p>
        </div>
        
        <h3>Expense Details</h3>
        
        @if($expenses->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>User</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                        <tr>
                            <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                            <td>{{ $expense->title }}</td>
                            <td>{{ $expense->category }}</td>
                            <td>{{ $expense->user->name }}</td>
                            <td>${{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No expenses recorded for this period.</p>
        @endif
        
        <div class="footer">
            <p>This is an automated report from the Expense Management System.</p>
            <p>© {{ date('Y') }} {{ $company->name }}</p>
        </div>
    </div>
</body>
</html>
