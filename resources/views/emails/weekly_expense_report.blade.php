<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Expense Report</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            color: #7f8c8d;
            margin-top: 0;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
        }

        /* Report Summary */
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
            padding: 10px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .summary-label {
            font-size: 14px;
            color: #7f8c8d;
        }

        /* Expense Table */
        .expense-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .expense-table th {
            background: #3498db;
            color: white;
            text-align: left;
            padding: 12px 15px;
        }

        .expense-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        .expense-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .expense-table tr:hover {
            background-color: #f1f1f1;
        }

        .amount {
            text-align: right;
            font-weight: bold;
        }

        /* Category Breakdown */
        .category-chart {
            margin-top: 30px;
        }

        .category-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .category-bar {
            height: 20px;
            background: #3498db;
            border-radius: 3px;
            margin-right: 10px;
        }

        .category-label {
            min-width: 120px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #7f8c8d;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .expense-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="company-name">{{ $company->name }}</div>
            <h2>Weekly Expense Report</h2>
        </div>

        <table class="expense-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Employee</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->created_at->format('M j') }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ $expense->user->name }}</td>
                        <td class="amount">${{ number_format($expense->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>This report was generated automatically on {{ now()->format('M j, Y \a\t g:i A') }}</p>
            <p>&copy; {{ date('Y') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
