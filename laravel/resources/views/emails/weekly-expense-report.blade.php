<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
</head>
<body>
<h1>Weekly Expense Report for {{ $admin->name }}</h1>

<p>Dear {{ $admin->name }},</p>

<p>Below is the summary of expenses for the past week:</p>

<table border="1" cellpadding="5">
    <thead>
    <tr>
        <th>Title</th>
        <th>Amount</th>
        <th>Category</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($report as $expense)
    <tr>
        <td>{{ $expense->title }}</td>
        <td>{{ $expense->amount }}</td>
        <td>{{ $expense->category }}</td>
        <td>{{ $expense->created_at->format('Y-m-d') }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

<p>Best regards,<br>
    Your Expense Management Team</p>
</body>
</html>
