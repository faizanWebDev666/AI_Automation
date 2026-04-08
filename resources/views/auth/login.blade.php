<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — AI Automation</title>
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


        .auth-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 30px rgba(0,0,0,0.06);
            text-align: center;
        }
        .auth-card .icon { font-size: 44px; margin-bottom: 12px; }
        .auth-card h1 {
            font-size: 26px; font-weight: 800; margin-bottom: 6px;
            background: linear-gradient(135deg, #1e293b, #6366f1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .auth-card p.subtitle { font-size: 14px; color: #94a3b8; margin-bottom: 28px; }
        .form-group { text-align: left; margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 6px; }
        .form-group input {
            width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 12px 16px; font-size: 14px; font-family: 'Inter', sans-serif; color: #1e293b;
            outline: none; transition: all 0.3s;
        }
        .form-group input:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); background: #fff; }
        .error-text { color: #dc2626; font-size: 12px; margin-top: 4px; }
        .remember-row {
            display: flex; align-items: center; gap: 8px; margin-bottom: 20px; text-align: left;
        }
        .remember-row input { width: auto; }
        .remember-row label { font-size: 13px; color: #64748b; margin: 0; }
        .btn-submit {
            width: 100%; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none;
            border-radius: 12px; padding: 14px; font-size: 15px; font-weight: 600;
            font-family: 'Inter', sans-serif; color: #fff; cursor: pointer; transition: all 0.3s; margin-top: 6px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.3); }
        .auth-footer { margin-top: 20px; font-size: 13px; color: #94a3b8; }
        .auth-footer a { color: #6366f1; text-decoration: none; font-weight: 500; }
        .auth-footer a:hover { text-decoration: underline; }

        .divider {
            display: flex; align-items: center; text-align: center; margin: 24px 0;
            color: #94a3b8; font-size: 12px; font-weight: 500;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; border-bottom: 1px solid #e2e8f0;
        }
        .divider:not(:empty)::before { margin-right: 12px; }
        .divider:not(:empty)::after { margin-left: 12px; }

        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 12px; font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif;
            color: #1e293b; cursor: pointer; transition: all 0.3s; text-decoration: none;
        }
        .btn-google:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-google img { width: 18px; height: 18px; }
    </style>
</head>
<body>
    <div class="auth-card">
          <div class="icon">
            <img src="images/logo.png" alt="Logo">
        </div>
        <h1>Welcome Back</h1>
        <p class="subtitle">Log in to continue chatting</p>

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                @error('email') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                @error('password') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn-submit">Log In</button>
        </form>

        <div class="divider">OR</div>

        <a href="{{ route('auth.google') }}" class="btn-google">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
            Continue with Google
        </a>

        <div class="auth-footer">
            Don't have an account? <a href="/register">Sign up</a>
        </div>
    </div>
</body>
</html>
