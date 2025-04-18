<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        p {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .total {
            font-weight: bold;
            text-align: right;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Weekly Expense Report</h1>
        <p><strong>Admin Name:</strong> {{ $reportData['adminName'] }}</p>
        <p><strong>Date Range:</strong> {{ $reportData['startDate'] }} - {{ $reportData['endDate'] }}</p>

        <h2>Expense Details</h2>
        @if (count($reportData['expenses']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Expense Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData['expenses'] as $expense)
                        <tr>
                            <td>{{ $expense->title }}</td>
                            <td>{{ $expense->category }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->expense_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="total">Total Expenses: {{ number_format($reportData['totalExpenses'], 2) }}</p>
        @else
            <p>No expenses found for this period.</p>
        @endif
    </div>
</body>
</html>
