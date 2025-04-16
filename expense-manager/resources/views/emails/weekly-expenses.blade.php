<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Weekly Expense Report</h2>
        <p>Hi {{ $reportData['admin']->name }},</p>
        <p>Your weekly expense report for <strong>{{ $reportData['company']->name }}</strong> is ready for the period:</p>
        <p><strong>{{ $reportData['startDate']->format('M d') }} - {{ $reportData['endDate']->format('M d, Y') }}</strong></p>
    </div>

    <p>Please find the attached PDF report with all details.</p>

    <p>Thanks,<br>The Expense Management System</p>

    <div class="footer">
        <p>This is an automated message. Please do not reply.</p>
    </div>
</body>
</html>
