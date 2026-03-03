@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Attendance Entry</h2>
    <p class="muted">Enter employee IDs as: 1,2,3 or 1+2+3 (both formats are supported).</p>

    <form action="{{ route('attendance.store') }}" method="POST">
        @csrf
        <div class="row">
            <div>
                <label>Attendance Date</label>
                <input type="date" name="attendance_date" value="{{ old('attendance_date', now()->toDateString()) }}" required>
            </div>
        </div>

        <div style="margin-top:12px;">
            <label>Full Time IDs</label>
            <textarea name="full_time_ids" rows="3" placeholder="1,2,3 or 1+2+3">{{ old('full_time_ids') }}</textarea>
            <div class="muted">Full daily salary for each ID.</div>
        </div>

        <div style="margin-top:12px;">
            <label>Half Time IDs</label>
            <textarea name="half_time_ids" rows="3" placeholder="4,5,6 or 4+5+6">{{ old('half_time_ids') }}</textarea>
            <div class="muted">Half of daily salary for each ID.</div>
        </div>

        <div style="margin-top:12px;">
            <label>Overtime (1, 2, or 3 hours @ {{ number_format($overtimeHourlyRate, 2) }} SAR/hour)</label>
            <textarea name="overtime_data" rows="3" placeholder="7:1 8:2 9:3">{{ old('overtime_data') }}</textarea>
            <div class="muted">Format: ID:HOURS. Examples: 7:1 (ID 7, 1 hour), 8:2 (ID 8, 2 hours), 9:3 (ID 9, 3 hours). Separate multiple by space.</div>
        </div>

        <div style="margin-top:12px;">
            <button class="btn" type="submit">Save Attendance</button>
        </div>
    </form>
</div>
@endsection
