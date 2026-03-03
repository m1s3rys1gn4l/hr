@extends('layouts.app')

@section('content')
<div class="card" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h2>Active Employees</h2>
        <p class="muted">Search and manage active employees</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a class="btn btn-secondary" href="{{ route('employees.left') }}">Left Employees</a>
        <a class="btn" href="{{ route('employees.create') }}">Add Employee</a>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('employees.index') }}" class="row">
        <div>
            <label>Search (ID, Name, Phone)</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="e.g. 1 or Ahmed">
        </div>
        <div>
            <label>Show</label>
            <select name="per_page">
                <option value="50" {{ $perPage === '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage === '100' ? 'selected' : '' }}>100</option>
                <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
            </select>
        </div>
        <div style="display:flex;align-items:end;gap:8px;">
            <button class="btn" type="submit">Apply</button>
            <a class="btn btn-secondary" href="{{ route('employees.index') }}">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>Employee List</h3>
    <table>
        <thead>
        <tr>
            <th>Unique ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Daily Salary</th>
            <th>Current Balance</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($activeEmployees as $employee)
            <tr>
                <td>{{ $employee->employee_code }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->phone ?: '-' }}</td>
                <td>{{ number_format($employee->daily_salary, 2) }} SAR</td>
                <td>{{ number_format($employee->current_balance, 2) }} SAR</td>
                <td>
                    <a class="btn btn-secondary" href="{{ route('employees.show', $employee) }}">Profile</a>
                    <form action="{{ route('employees.mark-left', $employee) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger" type="submit">Set Left</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No active employees.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if(method_exists($activeEmployees, 'links'))
        <div style="margin-top:10px;">{{ $activeEmployees->links() }}</div>
    @endif
</div>
@endsection
