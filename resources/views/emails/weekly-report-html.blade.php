<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Weekly Expense Report for {{ $companyName }}</h1>

    <p>Here is a summary of expenses incurred last week:</p>

    @if ($expenses->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->title }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ $expense->amount }}</td>
                        <td>{{ $expense->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No expenses were recorded last week.</p>
    @endif

    <p>Thank you,<br>The Expense Management System</p>
</body>
</html>