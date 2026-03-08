@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0;">Admin Profile</h2>
            <p class="muted" style="margin:6px 0 0;">Your administrator account details.</p>
        </div>
        <a class="btn" href="{{ route('admin.edit-profile') }}">Edit Profile</a>
    </div>
</div>

<div class="card">
    <div class="row" style="margin-top: 12px;">
        <div><strong>Name:</strong> {{ $user->name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Role:</strong> {{ $user->is_admin ? 'Admin' : 'User' }}</div>
        <div><strong>Status:</strong> <span style="color: {{ $user->is_active ? 'green' : 'red' }};">{{ $user->is_active ? 'Active' : 'Disabled' }}</span></div>
        <div><strong>Created At:</strong> {{ optional($user->created_at)->format('Y-m-d H:i') ?: '-' }}</div>
    </div>
</div>
@endsection
