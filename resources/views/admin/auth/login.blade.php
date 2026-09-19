<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EzyTemplate Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/assets/logo-icon.png">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #eef5ff 0%, #f7f2ff 100%);
            min-height: 100vh;
            display: grid;
            place-items: center;
            color: #0f172a;
            zoom: 1.08;
            padding: 20px;
        }

        .box {
            width: min(440px, 100%);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
            text-align: center;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .logo-wrap img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        h2 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 13px;
            color: #64748b;
            margin: 0 0 24px;
        }

        .err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 18px;
            text-align: left;
        }

        .field {
            margin: 16px 0;
            text-align: left;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #334155;
        }

        .field input {
            width: 100%;
            height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0 14px;
            font: 14px Inter, sans-serif;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field input:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .btn {
            width: 100%;
            height: 46px;
            border: 0;
            border-radius: 10px;
            background: #2563eb;
            color: #fff;
            font: 700 13px Inter, sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .foot {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            margin: 20px 0 0;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <form class="box" method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="logo-wrap">
            <img src="/assets/logo.png" alt="EzyTemplate">
        </div>
        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to manage your marketplace.</p>

        @if ($errors->any())
            <div class="err">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <div class="field">
            <label>Email</label>
            <input type="email" name="email"
                value="{{ old('email', env('ADMIN_EMAIL', 'admin@ezytemplate.local')) }}" required autofocus>
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn">Sign in to Dashboard</button>

        <p class="foot">EzyTemplate Admin · by Ezystore</p>
    </form>
</body>

</html>
