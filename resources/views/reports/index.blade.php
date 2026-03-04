@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Reports</h2>
    <p class="muted">Date: {{ $today }} | Month: {{ $monthLabel }}</p>
    
    <form method="GET" action="{{ route('reports.index') }}" style="margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap; align-items: flex-end;">
        <div>
            <label for="from_date" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">From Date</label>
            <input type="date" id="from_date" name="from_date" value="{{ $fromDate }}" style="padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;">
        </div>
        <div>
            <label for="to_date" style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">To Date</label>
            <input type="date" id="to_date" name="to_date" value="{{ $toDate }}" style="padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 13px;">
        </div>
        <button type="submit" style="background: #2563eb; color: #fff; border: none; border-radius: 4px; padding: 6px 12px; cursor: pointer; font-size: 13px; font-weight: 600;">Filter</button>
        <a href="{{ route('reports.index') }}" style="background: #6b7280; color: #fff; border: none; border-radius: 4px; padding: 6px 12px; text-decoration: none; display: inline-block; font-size: 13px; font-weight: 600;">Reset</a>
    </form>
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
    <div style="margin-top: 12px; text-align: center; font-size: 12px;">
        Page {{ $recentAttendance->currentPage() }} of {{ $recentAttendance->lastPage() }} 
        @if($recentAttendance->onFirstPage())
            <span style="color: #9ca3af;">← Prev</span>
        @else
            <a href="{{ $recentAttendance->previousPageUrl() }}" style="color: #2563eb; text-decoration: none;">← Prev</a>
        @endif
        | 
        @if($recentAttendance->hasMorePages())
            <a href="{{ $recentAttendance->nextPageUrl() }}" style="color: #2563eb; text-decoration: none;">Next →</a>
        @else
            <span style="color: #9ca3af;">Next →</span>
        @endif
    </div>
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
    <div style="margin-top: 12px; text-align: center; font-size: 12px;">
        Page {{ $recentPayouts->currentPage() }} of {{ $recentPayouts->lastPage() }} 
        @if($recentPayouts->onFirstPage())
            <span style="color: #9ca3af;">← Prev</span>
        @else
            <a href="{{ $recentPayouts->previousPageUrl() }}" style="color: #2563eb; text-decoration: none;">← Prev</a>
        @endif
        | 
        @if($recentPayouts->hasMorePages())
            <a href="{{ $recentPayouts->nextPageUrl() }}" style="color: #2563eb; text-decoration: none;">Next →</a>
        @else
            <span style="color: #9ca3af;">Next →</span>
        @endif
    </div>
</div>
@endsection
