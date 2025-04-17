<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Expense Report</title>
</head>

<body>
    <h1>Weekly Expense Report</h1>
    <p>Total expenses this week: {{ $totalExpensesCreated }}</p>
    <p>Total amount: ${{ number_format($totalAmountSpent, 2) }}</p>
</body>

</html>