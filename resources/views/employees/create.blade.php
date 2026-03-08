@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Create Employee</h2>
    <p class="muted">Enter a unique employee ID (1 to 999). IDs from left employees can be reused.</p>

    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="row">
            <div>
                <label>Employee ID (required)</label>
                <input type="number" name="employee_code" value="{{ old('employee_code') }}" min="1" max="999" required>
                @error('employee_code')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px;">
            <div>
                <label>Name (required)</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label>Phone (optional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}">
            </div>
        </div>

        <div class="row" style="margin-top:12px;">
            <div>
                <label>Daily Salary (SAR)</label>
                <input type="number" name="daily_salary" value="{{ old('daily_salary') }}" step="0.01" min="0" required>
            </div>
        </div>

        <div style="margin-top:12px;">
            <button class="btn" type="submit">Create Employee</button>
            <a class="btn btn-secondary" href="{{ route('employees.index') }}">Back</a>
        </div>
    </form>
</div>
@endsection
