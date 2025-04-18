<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding: 20px;
            background-color: #f5f5f5;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .summary h2 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .total {
            font-weight: bold;
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Weekly Expense Report</h1>
        <h3>{{ $company->name }}</h3>
        <p>Period: {{ now()->subWeek()->format('M d, Y') }} - {{ now()->format('M d, Y') }}</p>
    </div>
    
    <p>Hello {{ $admin->name }},</p>
    
    <p>Here is the weekly expense report for {{ $company->name }}. Below you'll find a summary of expenses created during the past week.</p>
    
    <div class="summary">
        <h2>Summary</h2>
        <p>Total Expenses: <span class="total">${{ number_format($totalAmount, 2) }}</span></p>
        <p>Number of Expenses: {{ $expenses->count() }}</p>
        
        <h3>Breakdown by Category</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Count</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenseSummary as $category => $summary)
                <tr>
                    <td>{{ $category }}</td>
                    <td>{{ $summary['count'] }}</td>
                    <td>${{ number_format($summary['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <h2>Expense Details</h2>
    
    @if($expenses->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Created By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->title }}</td>
                <td>${{ number_format($expense->amount, 2) }}</td>
                <td>{{ $expense->category }}</td>
                <td>{{ $expense->user->name }}</td>
                <td>{{ $expense->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No expenses were recorded during this period.</p>
    @endif
    
    <p>This is an automated report. Please do not reply to this email.</p>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ $company->name }} - Expense Management System</p>
    </div>
</body>
</html> 