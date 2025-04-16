<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Weekly Expense Report</h2>
    <p>Admin: {{ $admin->name }}</p>
    <p>Company: {{ $admin->company->name ?? 'N/A' }}</p>

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
                    <td>{{ $expense->user->name ?? 'N/A' }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>₦{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
