<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Expense Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        th { background-color: #f8f8f8; }
    </style>
</head>
<body>
    <h2>Weekly Expense Report for {{ $admin->name }}</h2>
    <p>Total Expenses: {{ $expenses->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
                <tr>
                    <td>{{ $expense->title }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->category->name ?? 'N/A' }}</td>
                    <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
