<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function profile(Request $request)
    {
        return view('admin.profile', [
            'user' => $request->user(),
        ]);
    }

    public function index()
    {
        $users = User::query()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('admin.users.create', [
            'permissions' => $this->availablePermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $permissionKeys = collect($validated['permissions'] ?? []);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => (bool) ($validated['is_admin'] ?? false),
            'can_manage_employees' => $permissionKeys->contains('manage_employees'),
            'can_manage_payouts' => $permissionKeys->contains('manage_payouts'),
            'can_manage_attendance' => $permissionKeys->contains('manage_attendance'),
            'can_view_reports' => $permissionKeys->contains('view_reports'),
            'can_view_employee_profiles' => $permissionKeys->contains('view_employee_profiles'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    private function availablePermissions(): array
    {
        return [
            'manage_employees' => 'Employee Management',
            'manage_payouts' => 'Payout Management',
            'manage_attendance' => 'Attendance Management',
            'view_reports' => 'View Reports',
            'view_employee_profiles' => 'View Employee Profile',
        ];
    }
}
