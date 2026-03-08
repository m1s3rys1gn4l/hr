@extends('layouts.app')

@section('content')
<div class="card" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="margin:0;">User Management</h2>
        <p class="muted" style="margin:6px 0 0;">Create and manage users with module permissions.</p>
    </div>
    <a class="btn" href="{{ route('admin.users.create') }}">Create User</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Permissions</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                    <td>
                        @if($user->is_admin)
                            Full Access
                        @else
                            @php($perms = [])
                            @if($user->can_manage_employees) @php($perms[] = 'Employee Mgmt') @endif
                            @if($user->can_manage_payouts) @php($perms[] = 'Payout Mgmt') @endif
                            @if($user->can_manage_attendance) @php($perms[] = 'Attendance Mgmt') @endif
                            @if($user->can_view_reports) @php($perms[] = 'Reports') @endif
                            @if($user->can_view_employee_profiles) @php($perms[] = 'Employee Profile') @endif
                            {{ count($perms) ? implode(', ', $perms) : '-' }}
                        @endif
                    </td>
                    <td>
                        <span style="color: {{ $user->is_active ? 'green' : 'red' }};">
                            {{ $user->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </td>
                    <td>{{ optional($user->created_at)->format('Y-m-d H:i') ?: '-' }}</td>
                    <td>
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn" style="background-color: {{ $user->is_active ? '#dc3545' : '#28a745' }}; padding: 4px 8px; font-size: 0.875rem;">
                                    {{ $user->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        @else
                            <span class="muted" style="font-size: 0.875rem;">Your Account</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(method_exists($users, 'links'))
        <div style="margin-top: 12px;">
            {{ $users->links('pagination::simple-default') }}
        </div>
    @endif
</div>
@endsection
