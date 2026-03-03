@extends('layouts.app')

@section('content')
<div class="card" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h2>Left Employees</h2>
        <p class="muted">Reactivate employees with previous or new unique ID</p>
    </div>
    <a class="btn btn-secondary" href="{{ route('employees.index') }}">Active Employees</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('employees.left') }}" class="row">
        <div>
            <label>Search (Previous ID, Name, Phone)</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="e.g. 2 or Fatima">
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
            <a class="btn btn-secondary" href="{{ route('employees.left') }}">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <table>
        <thead>
        <tr>
            <th>Previous ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Left Date</th>
            <th>Re-Activate</th>
            <th>Profile</th>
        </tr>
        </thead>
        <tbody>
        @forelse($leftEmployees as $employee)
            <tr>
                <td>{{ $employee->released_employee_code ?: '-' }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->phone ?: '-' }}</td>
                <td>{{ optional($employee->left_at)->format('Y-m-d H:i') ?: '-' }}</td>
                <td>
                    <form action="{{ route('employees.reactivate', $employee) }}" method="POST" style="display:flex;gap:8px;align-items:center;">
                        @csrf
                        @method('PATCH')
                        <select name="id_strategy" style="max-width:180px;">
                            <option value="previous">Use Previous ID</option>
                            <option value="new">Use New ID</option>
                        </select>
                        <button class="btn" type="submit">Activate</button>
                    </form>
                </td>
                <td><a class="btn btn-secondary" href="{{ route('employees.show', $employee) }}">Profile</a></td>
            </tr>
        @empty
            <tr><td colspan="6">No left employees.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if(method_exists($leftEmployees, 'links'))
        <div style="margin-top:10px;">{{ $leftEmployees->links() }}</div>
    @endif
</div>
@endsection
