<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; text-align: right; }
    </style>
</head>
<body>
<div class="container">
    <h2>Weekly Expense Report</h2>
    <p>Hello {{ $adminName }},</p>
    <p>Here is your weekly expense report for <strong>{{ $companyName }}</strong>
        covering the period {{ $reportPeriod }}:</p>

    <table>
        <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->title }}</td>
                <td>{{ $expense->category }}</td>
                <td>{{ number_format($expense->amount, 2) }}</td>
                <td>{{ $expense->created_at->format('M d, Y') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2" class="total">Total:</td>
            <td colspan="2">{{ number_format($totalAmount, 2) }}</td>
        </tr>
        </tfoot>
    </table>

    <p>Thank you,</p>
    <p>{{ config('app.name') }}</p>
</div>
</body>
</html>
