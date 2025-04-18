<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="background-color:#f8fafc;padding:30px;font-family:sans-serif;">
    <div style="max-width:600px;margin:0 auto;background:white;padding:30px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,0.05);">
        <h2 style="font-size:24px;font-weight:700;color:#1f2937;margin-bottom:10px;">Hi {{ $name }},</h2>
        <p style="color:#4b5563;font-size:16px;margin-bottom:20px;">
            You recently requested to reset your password for your <strong>Multi-Tenant SaaS</strong> account.
        </p>
        <p style="margin-bottom:30px;">
            <a href="{{ $url }}" style="background-color:#3b82f6;color:white;padding:12px 20px;border-radius:6px;text-decoration:none;display:inline-block;font-weight:600;">
                Reset Password
            </a>
        </p>
        <p style="color:#6b7280;font-size:14px;">
            If you did not request a password reset, please ignore this email or contact support if you have questions.
        </p>
        <p style="color:#9ca3af;font-size:12px;margin-top:40px;">— The Multi-Tenant SaaS Team</p>
    </div>
</body>
</html>
