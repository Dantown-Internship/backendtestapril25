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
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            background-color: #f5f5f5;
            padding: 20px;
            border-bottom: 2px solid #ddd;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 30px;
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
            background-color: #f5f5f5;
        }
        .category-section, .user-section {
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Weekly Expense Report</h1>
        <p>Period: {{ $reportData['startDate']->format('M d') }} - {{ $reportData['endDate']->format('M d, Y') }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <p><strong>Total Expenses:</strong> {{ count($reportData['expenses']) }}</p>
        <p><strong>Total Amount:</strong> ${{ number_format($reportData['totalAmount'], 2) }}</p>
    </div>

    <div class="category-section">
        <h2>Expenses by Category</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Count</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['categoryTotals'] as $category => $data)
                <tr>
                    <td>{{ $category }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>${{ number_format($data['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="user-section">
        <h2>Expenses by User</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Count</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['userTotals'] as $userData)
                <tr>
                    <td>{{ $userData['user'] }}</td>
                    <td>{{ $userData['count'] }}</td>
                    <td>${{ number_format($userData['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="expenses-section">
        <h2>All Expenses</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['expenses'] as $expense)
                <tr>
                    <td>{{ $expense->created_at->format('M d, Y') }}</td>
                    <td>{{ $expense->user->name }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is an automated report. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} Expense Management System</p>
    </div>
</body>
</html>
