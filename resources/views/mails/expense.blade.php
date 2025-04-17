<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report</title>
</head>

<body>
    <h1>Hello {{ $admin->name ?? 'Admin' }},</h1>   
    <p>This is your weekly expense report.</p>
    <p>Total Expenses: <strong>{{ $total_expenses }}</strong></p>

    Thanks,
</body>

</html>