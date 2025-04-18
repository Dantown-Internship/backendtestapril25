<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Expense Report</title>
</head>
<body>
<h2>Weekly Expense Report for {{ $companyName ?? 'Your Company' }}</h2>
<!-- <ul>
    @foreach ($expenses as $expense)
        <li>{{ $expense->title }} - ₦{{ number_format($expense->amount, 2) }} ({{ $expense->category }}) by {{ $expense->user->name }}</li>
    @endforeach
</ul> -->
</body>
</html>