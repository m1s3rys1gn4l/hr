@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Reports</h2>
    <p class="muted">Date: {{ $today }} | Month: {{ $monthLabel }}</p>
</div>

<div class="grid">
    <div class="card"><strong>Active Employees:</strong><br>{{ $activeEmployees }}</div>
    <div class="card"><strong>Left Employees:</strong><br>{{ $leftEmployees }}</div>
    <div class="card"><strong>Total Balance:</strong><br>{{ number_format($totalBalance, 2) }} SAR</div>
    <div class="card"><strong>Today's Attendance Credit:</strong><br>{{ number_format($todayAttendanceCredit, 2) }} SAR</div>
    <div class="card"><strong>This Month Attendance Credit:</strong><br>{{ number_format($monthAttendanceCredit, 2) }} SAR</div>
    <div class="card"><strong>Today's Payout:</strong><br>{{ number_format($todayPayout, 2) }} SAR</div>
    <div class="card"><strong>This Month Payout:</strong><br>{{ number_format($monthPayout, 2) }} SAR</div>
    <div class="card"><strong>Total Payout (All Time):</strong><br>{{ number_format($totalPayout, 2) }} SAR</div>
</div>

<div class="card">
    <h3>Recent Attendance</h3>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Overtime</th>
            <th>Credited</th>
        </tr>
        </thead>
        <tbody>
        @forelse($recentAttendance as $record)
            <tr>
                <td>{{ $record->attendance_date?->format('Y-m-d') }}</td>
                <td>{{ $record->employee?->name ?? '-' }}</td>
                <td>{{ ucfirst($record->attendance_type) }}</td>
                <td>{{ $record->overtime_hours }}</td>
                <td>{{ number_format($record->credited_amount, 2) }} SAR</td>
            </tr>
        @empty
            <tr><td colspan="5">No attendance records found.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($recentAttendance->hasPages())
        <div style="margin-top:12px; text-align:center;">
            {{ $recentAttendance->links() }}
        </div>
    @endif
</div>

<div class="card">
    <h3>Recent Payouts</h3>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Employee</th>
            <th>Amount</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
        @forelse($recentPayouts as $payout)
            <tr>
                <td>{{ $payout->paid_at?->format('Y-m-d') }}</td>
                <td>{{ $payout->employee?->name ?? '-' }}</td>
                <td>{{ number_format($payout->amount, 2) }} SAR</td>
                <td>{{ $payout->note ?: '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="4">No payout records found.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($recentPayouts->hasPages())
        <div style="margin-top:12px; text-align:center;">
            {{ $recentPayouts->links() }}
        </div>
    @endif
</div>
@endsection
