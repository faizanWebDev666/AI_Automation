<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — AI Automation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
    background: url('https://images.unsplash.com/photo-1560185127-6ed189bf02f4') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
/* Blur overlay */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;

    backdrop-filter: blur(3px);
    background: rgba(0, 0, 0, 0.3); /* dark overlay for better readability */
    
    z-index: 0;
}

/* Make content appear above blur */
body > * {
    position: relative;
    z-index: 1;
}
        .verify-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 44px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 30px rgba(0,0,0,0.06);
            text-align: center;
        }
        .verify-card .icon { font-size: 56px; margin-bottom: 16px; }
        .verify-card h1 {
            font-size: 26px; font-weight: 800; margin-bottom: 8px;
            background: linear-gradient(135deg, #1e293b, #6366f1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .verify-card .subtitle { font-size: 14px; color: #64748b; margin-bottom: 8px; line-height: 1.6; }
        .email-highlight { font-weight: 600; color: #6366f1; }
        .message {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
            padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #166534; text-align: left;
        }
        .error {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
            padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #991b1b; text-align: left;
        }
        .btn-group { display: flex; gap: 12px; margin-top: 24px; flex-direction: column; }
        .btn {
            flex: 1; border: none; border-radius: 12px; padding: 14px; font-size: 15px; font-weight: 600;
            font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.3s; white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.3); }
        .btn-secondary {
            background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;
        }
        .btn-secondary:hover { background: #e2e8f0; }
        .resend-link { font-size: 13px; color: #94a3b8; margin-top: 16px; }
        .resend-link a { color: #6366f1; text-decoration: none; font-weight: 500; cursor: pointer; }
        .resend-link a:hover { text-decoration: underline; }
        .footer { margin-top: 24px; font-size: 12px; color: #94a3b8; }
        .footer a { color: #6366f1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="icon">
            <img src="images/logo.png" alt="Logo">
        </div>
        <h1>Verify Your Email</h1>
        <p class="subtitle">
            A verification link has been sent to<br>
            <span class="email-highlight">{{ Auth::user()->email }}</span>
        </p>

        @if ($message = Session::get('message'))
            <div class="message">
                {{ $message }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <p style="font-size: 13px; color: #94a3b8; margin-top: 16px;">
            📬 Check your inbox and click the verification link to continue.<br>
            <strong>Don't forget to check</strong> your spam folder!
        </p>

        <div class="btn-group">
            <form method="POST" action="/email/resend" style="width: 100%;">
                @csrf
                <button type="submit" class="btn btn-primary">Resend Verification Email</button>
            </form>
        </div>

        <div class="resend-link">
            <p style="margin-bottom: 12px;">Email verified?</p>
            <a href="/login">← Back to Login</a>
        </div>

        <div class="footer">
            <form method="POST" action="/logout" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: #6366f1; cursor: pointer; font-weight: 500;">Log out</button>
            </form>
        </div>
</body>
</html>
