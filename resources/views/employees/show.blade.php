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
    <form method="GET" action="{{ route('employees.show', $employee) }}" class="row">
        <div>
            <label for="from_datetime">From (Date & Time)</label>
            <input type="datetime-local" id="from_datetime" name="from_datetime" value="{{ $fromDateTime }}">
        </div>
        <div>
            <label for="to_datetime">To (Date & Time)</label>
            <input type="datetime-local" id="to_datetime" name="to_datetime" value="{{ $toDateTime }}">
        </div>
        <div style="display:flex; align-items:end; gap:8px;">
            <button class="btn" type="submit">Apply Filter</button>
            <a class="btn btn-secondary" href="{{ route('employees.show', $employee) }}">Reset</a>
            <a class="btn" href="{{ route('employees.export-history', $employee, request()->query()) }}">Export Excel (.xlsx)</a>
        </div>
    </form>
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
    @if($attendanceHistory->hasPages())
        <div style="margin-top:10px; text-align:center; font-size:12px;">
            Page {{ $attendanceHistory->currentPage() }} of {{ $attendanceHistory->lastPage() }}
            @if($attendanceHistory->onFirstPage())
                <span style="color:#9ca3af; margin-left:8px;">← Prev</span>
            @else
                <a href="{{ $attendanceHistory->previousPageUrl() }}" style="margin-left:8px; color:#2563eb; text-decoration:none;">← Prev</a>
            @endif
            |
            @if($attendanceHistory->hasMorePages())
                <a href="{{ $attendanceHistory->nextPageUrl() }}" style="color:#2563eb; text-decoration:none;">Next →</a>
            @else
                <span style="color:#9ca3af;">Next →</span>
            @endif
        </div>
    @endif
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
    @if($payoutHistory->hasPages())
        <div style="margin-top:10px; text-align:center; font-size:12px;">
            Page {{ $payoutHistory->currentPage() }} of {{ $payoutHistory->lastPage() }}
            @if($payoutHistory->onFirstPage())
                <span style="color:#9ca3af; margin-left:8px;">← Prev</span>
            @else
                <a href="{{ $payoutHistory->previousPageUrl() }}" style="margin-left:8px; color:#2563eb; text-decoration:none;">← Prev</a>
            @endif
            |
            @if($payoutHistory->hasMorePages())
                <a href="{{ $payoutHistory->nextPageUrl() }}" style="color:#2563eb; text-decoration:none;">Next →</a>
            @else
                <span style="color:#9ca3af;">Next →</span>
            @endif
        </div>
    @endif
</div>
@endsection
