<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f6f8fb; color: #222; display: grid; min-height: 100vh; place-items: center; }
        .card { width: 100%; max-width: 420px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px; }
        h2 { margin-top: 0; }
        label { display: block; font-weight: 600; margin-bottom: 6px; }
        input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 12px; box-sizing: border-box; }
        .remember { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .remember input { width: auto; margin: 0; }
        button { width: 100%; background: #2563eb; color: #fff; border: 0; border-radius: 6px; padding: 10px; font-weight: 600; cursor: pointer; }
        .error { color: #b91c1c; font-size: 13px; margin-bottom: 10px; }
        .muted { color: #6b7280; font-size: 13px; margin-top: 12px; }
    </style>
</head>
<body>
<div class="card">
    <h2>System Login</h2>

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label class="remember" for="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            <span>Remember me</span>
        </label>

        <button type="submit">Sign in</button>
    </form>

    <p class="muted">Sign in with your assigned account.</p>
</div>
</body>
</html>
