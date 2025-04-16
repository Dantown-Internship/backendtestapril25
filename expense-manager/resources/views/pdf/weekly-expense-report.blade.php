<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #555;
        }
        .date-range {
            font-size: 14px;
            color: #777;
        }
        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="report-title">Weekly Expense Report</div>
        <div class="date-range">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</div>
    </div>

    <div class="summary">
        <div class="summary-title">Summary</div>
        <p>Total Expenses: {{ count($expenses) }}</p>
        <p>Total Amount: ${{ number_format($totalAmount, 2) }}</p>
    </div>

    <h3>Expenses by Category</h3>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Count</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryTotals as $category => $data)
                <tr>
                    <td>{{ $category }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>${{ number_format($data['total'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total</td>
                <td>{{ count($expenses) }}</td>
                <td>${{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Expenses by User</h3>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Count</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userTotals as $data)
                <tr>
                    <td>{{ $data['user'] }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>${{ number_format($data['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Detailed Expenses</h3>
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
                    <td>{{ $expense->created_at->format('M d, Y') }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->user->name }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated on {{ now()->format('M d, Y H:i:s') }}</p>
        <p>Expense Management System</p>
    </div>
</body>
</html>
