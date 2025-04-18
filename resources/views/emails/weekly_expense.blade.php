<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Weekly Expense Report</title>
</head>
<body>
    <h2>Hello {{ $admin->name }},</h2>

    <p>Here is the weekly expense report for your company: <strong>{{ $admin->company->name }}</strong>.</p>

    <h3>Total Expenses: ${{ number_format($total, 2) }}</h3>

    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Submitted By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{ $expense->title }}</td>
                    <td>${{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->user->name }}</td>
                    <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No expenses submitted this week.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="margin-top: 20px;">Thank you</p>
</body>
</html>
