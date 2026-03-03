@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Employee Profile</h2>
    <div class="row">
        <div><strong>Current ID:</strong> {{ $employee->employee_code ?: '-' }}</div>
        <div><strong>Previous ID:</strong> {{ $employee->released_employee_code ?: '-' }}</div>
        <div><strong>Name:</strong> {{ $employee->name }}</div>
        <div><strong>Phone:</strong> {{ $employee->phone ?: '-' }}</div>
        <div><strong>Status:</strong> {{ ucfirst($employee->status) }}</div>
        <div><strong>Daily Salary:</strong> {{ number_format($employee->daily_salary, 2) }} SAR</div>
        <div><strong>Current Balance:</strong> {{ number_format($employee->current_balance, 2) }} SAR</div>
        <div><strong>Last Payout:</strong> {{ optional($employee->payouts()->latest('paid_at')->first()?->paid_at)->format('Y-m-d H:i') ?: '-' }}</div>
    </div>

    @if($employee->status === 'active')
    <form action="{{ route('employees.mark-left', $employee) }}" method="POST" style="margin-top:12px;">
        @csrf
        @method('PATCH')
        <button class="btn btn-danger" type="submit">Set Employee to Left</button>
    </form>
    @endif
</div>

<div class="card">
    <h3>Attendance History</h3>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Overtime</th>
            <th>Credited Amount</th>
        </tr>
        </thead>
        <tbody>
        @forelse($attendanceHistory as $record)
            <tr>
                <td>{{ $record->attendance_date->format('Y-m-d') }}</td>
                <td>{{ ucfirst($record->attendance_type) }}</td>
                <td>{{ $record->overtime_hours > 0 ? $record->overtime_hours . ' hour(s) (+' . ($record->overtime_hours * 10) . ' SAR)' : 'No' }}</td>
                <td>{{ number_format($record->credited_amount, 2) }} SAR</td>
            </tr>
        @empty
            <tr><td colspan="4">No attendance records yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:10px;">{{ $attendanceHistory->links() }}</div>
</div>

<div class="card">
    <h3>Salary Payout History</h3>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
        @forelse($payoutHistory as $payout)
            <tr>
                <td>{{ $payout->paid_at->format('Y-m-d H:i') }}</td>
                <td>{{ number_format($payout->amount, 2) }} SAR</td>
                <td>{{ $payout->note ?: '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No payout records yet.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:10px;">{{ $payoutHistory->links() }}</div>
</div>
@endsection
