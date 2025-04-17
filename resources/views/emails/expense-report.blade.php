<!DOCTYPE html>
<html>
<head>
    <title>Weekly Expense Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background: #f8f9fa; padding: 10px; text-align: center; font-size: 0.8em; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Weekly Expense Report</h1>
        <p>{{ $timeframe }}</p>
    </div>

    <div class="content">
        <h2>Expense Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $category => $data)
                <tr>
                    <td>{{ $category }}</td>
                    <td>${{ number_format($data['total'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>${{ number_format($total, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }} &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>