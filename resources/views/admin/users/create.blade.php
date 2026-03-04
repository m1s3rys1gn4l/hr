@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Create User</h2>
    <p class="muted">Assign role and module permissions while creating a user.</p>

    <form method="POST" action="{{ route('admin.users.store') }}" style="margin-top: 12px;">
        @csrf

        <div class="row">
            <div>
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="row" style="margin-top: 12px;">
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
        </div>

        <div class="card" style="margin-top: 16px;">
            <h3 style="margin-top: 0;">Role</h3>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }} style="width:auto;">
                <span>Mark as Admin (full access)</span>
            </label>
        </div>

        <div class="card" style="margin-top: 16px;">
            <h3 style="margin-top: 0;">Permissions</h3>
            <p class="muted">These apply to non-admin users.</p>

            <div class="row" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                @foreach($permissions as $key => $label)
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $key }}"
                            {{ in_array($key, old('permissions', []), true) ? 'checked' : '' }}
                            style="width:auto;"
                        >
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="display:flex;gap:8px;margin-top:16px;">
            <button class="btn" type="submit">Create User</button>
            <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
