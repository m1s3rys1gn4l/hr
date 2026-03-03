@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Admin Dashboard</h2>
    <p class="muted">Currency: Saudi Riyal (SAR)</p>
</div>

<div class="grid">
    <div class="card"><strong>Active Employees:</strong><br>{{ $activeCount }}</div>
    <div class="card"><strong>Left Employees:</strong><br>{{ $leftCount }}</div>
    <div class="card"><strong>Total Salary Balance:</strong><br>{{ number_format($totalBalance, 2) }} SAR</div>
    <div class="card"><strong>Today's Payout:</strong><br>{{ number_format($todayPayout, 2) }} SAR</div>
</div>

<div class="card">
    <h3>Recent Employees</h3>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Daily Salary</th>
            <th>Balance</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($recentEmployees as $employee)
            <tr>
                <td>{{ $employee->employee_code ?: '-' }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ ucfirst($employee->status) }}</td>
                <td>{{ number_format($employee->daily_salary, 2) }} SAR</td>
                <td>{{ number_format($employee->current_balance, 2) }} SAR</td>
                <td><a class="btn btn-secondary" href="{{ route('employees.show', $employee) }}">Profile</a></td>
            </tr>
        @empty
            <tr><td colspan="6">No records yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
