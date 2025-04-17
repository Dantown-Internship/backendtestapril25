<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Weekly Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <p>Dear {{ $user->name }},</p>

        <p>Please find attached the weekly expense report containing all recorded expenses for this week.</p>

        <p>If you have any questions or need additional details, feel free to reach out.</p>

        <p>Best regards,<br>
        {{ config('app.name') }}</p>

        <div class="footer">
            This is an automated message. Please do not reply directly to this email.
        </div>
    </div>
</body>
</html>
