<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Employee Management' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f6f8fb; color: #222; }
        nav { background: #1f2937; padding: 12px 18px; display: flex; gap: 16px; justify-content: center; align-items: center; flex-wrap: wrap; }
        nav a { color: #fff; text-decoration: none; font-weight: 600; }
        .nav-logout { margin: 0; }
        .nav-logout button { background: transparent; color: #fff; border: 1px solid #fff; border-radius: 6px; padding: 6px 10px; cursor: pointer; font-weight: 600; }
        .container { max-width: 1100px; margin: 20px auto; padding: 0 16px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        .grid { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        .btn { background: #2563eb; color: #fff; border: none; border-radius: 6px; padding: 8px 12px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-danger { background: #dc2626; }
        .btn-secondary { background: #4b5563; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; }
        .row { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .muted { color: #6b7280; font-size: 13px; }
        .alert { background: #ecfeff; border: 1px solid #a5f3fc; color: #0e7490; padding: 10px; border-radius: 6px; margin-bottom: 12px; }
        .error { color: #b91c1c; font-size: 13px; margin-top: 4px; }
        .pagination { display: inline-flex; gap: 4px; list-style: none; margin: 0; padding: 0; }
        .pagination li { margin: 0; }
        .pagination a, .pagination span { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; text-decoration: none; color: #2563eb; font-size: 13px; display: inline-block; }
        .pagination a:hover { background: #f0f9ff; border-color: #2563eb; }
        .pagination span.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .pagination span.disabled { color: #9ca3af; cursor: not-allowed; border-color: #e5e7eb; }
    </style>
</head>
<body>
@auth
    <nav>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('employees.index') }}">Employees</a>
        <a href="{{ route('employees.left') }}">Left Employees</a>
        <a href="{{ route('attendance.create') }}">Attendance</a>
        <a href="{{ route('payouts.create') }}">Payouts</a>
        <a href="{{ route('reports.index') }}">Reports</a>
        <a href="{{ route('settings.edit') }}">Settings</a>
        <form method="POST" action="{{ route('logout') }}" class="nav-logout">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
@endauth
<div class="container">
    @if(session('status'))
        <div class="alert">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="card">
            <strong>Please fix these issues:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li class="error">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</div>
</body>
</html>
