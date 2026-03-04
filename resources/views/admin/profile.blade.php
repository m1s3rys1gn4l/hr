@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Admin Profile</h2>
    <p class="muted">Your administrator account details.</p>

    <div class="row" style="margin-top: 12px;">
        <div><strong>Name:</strong> {{ $user->name }}</div>
        <div><strong>Email:</strong> {{ $user->email }}</div>
        <div><strong>Role:</strong> {{ $user->is_admin ? 'Admin' : 'User' }}</div>
        <div><strong>Created At:</strong> {{ optional($user->created_at)->format('Y-m-d H:i') ?: '-' }}</div>
    </div>
</div>
@endsection
