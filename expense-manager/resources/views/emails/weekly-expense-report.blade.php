<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Weekly Expense Report</title>
</head>
<body>
    <h2>🧾 Your Weekly Expense Report</h2>

    @if ($expenses->isEmpty())
        <p>No expenses recorded this week.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Amount</th>
                    <th>Category</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->title }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->category }}</td>
                        <td>{{ $expense->date->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p>— Expense Management System</p>
</body>
</html>
