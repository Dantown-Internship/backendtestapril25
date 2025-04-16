<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Weekly Expense Report</h2>
        <p><strong>Admin:</strong> {{ $admin->name }}</p>
        <p><strong>Company:</strong> {{ $admin->company->name }}</p>

        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Category</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->user->name }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>₦{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ $expense->created_at->format('Y-m-d h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
