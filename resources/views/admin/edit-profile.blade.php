@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Edit Admin Profile</h2>
    <p class="muted">Update your name, email, and password.</p>

    <form action="{{ route('admin.update-profile') }}" method="POST">
        @csrf

        <div class="row" style="margin-top: 12px;">
            <div>
                <label>Name (required)</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row" style="margin-top: 12px;">
            <div>
                <label>Email (required)</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row" style="margin-top: 12px;">
            <div>
                <label>New Password (optional - leave blank to keep current)</label>
                <input type="password" name="password" placeholder="Enter new password">
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row" style="margin-top: 12px;">
            <div>
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm password">
                @error('password_confirmation')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="margin-top: 20px;">
            <button class="btn" type="submit">Update Profile</button>
            <a class="btn btn-secondary" href="{{ route('admin.profile') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
