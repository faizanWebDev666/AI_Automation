<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #d32f2f;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: white;
        }
        .warning {
            background-color: #ffebee;
            border-left: 4px solid #d32f2f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 15px 0;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Account Verification Banned</h1>
        </div>

        <div class="content">
            <p>Dear {{ $user->name }},</p>

            <p>We regret to inform you that your email address <strong>{{ $user->email }}</strong> has been <strong>banned from verification</strong> due to multiple failed verification attempts.</p>

            <div class="warning">
                <strong>Ban Reason:</strong><br>
                {{ $reason }}<br><br>
                <strong>Banned At:</strong><br>
                {{ $bannedAt->format('F j, Y \a\t g:i A') }}
            </div>

            <h3>What does this mean?</h3>
            <ul>
                <li>You cannot submit new verification documents with this email address</li>
                <li>You cannot upload products as a dealer with this email</li>
                <li>This email is blocked from dealer registration</li>
            </ul>

            <h3>What should you do?</h3>
            <p>To restore access, please contact our support team with the following information:</p>
            <ul>
                <li>Your email address: {{ $user->email }}</li>
                <li>Your account name: {{ $user->name }}</li>
                <li>A brief explanation of the verification difficulty you faced</li>
            </ul>

            <p>
                <a href="{{ route('contact-support') }}" class="button">Contact Support</a>
            </p>

            <h3>Why did this happen?</h3>
            <p>You have exceeded the maximum of <strong>5 verification attempts</strong>. This security measure is in place to prevent misuse and ensure the integrity of our dealer network.</p>

            <p>Our support team can help review your case and potentially appeal the ban if you believe this was an error.</p>

            <hr>

            <p>
                <strong>Note:</strong> If you need to create a new dealer account, you will need to use a different email address.
            </p>

            <p>
                Best regards,<br>
                <strong>AI Automation Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} AI Automation. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
