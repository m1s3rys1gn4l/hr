@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Settings</h2>
    <p class="muted">Manage application values used in salary calculations.</p>

    <form action="{{ route('settings.update') }}" method="POST" style="margin-top:12px; max-width:420px;">
        @csrf

        <div>
            <label>Overtime Hourly Rate (SAR)</label>
            <input
                type="number"
                name="overtime_hourly_rate"
                value="{{ old('overtime_hourly_rate', number_format($overtimeHourlyRate, 2, '.', '')) }}"
                min="0"
                max="10000"
                step="0.01"
                required
            >
            <div class="muted">This value is used in attendance overtime calculation: hours × rate.</div>
        </div>

        <div style="margin-top:12px;">
            <button class="btn" type="submit">Save Settings</button>
        </div>
    </form>
</div>
@endsection
