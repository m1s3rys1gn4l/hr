<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
	Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
	Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AdminAuthController::class, 'logout'])
	->middleware('auth')
	->name('logout');

Route::middleware('admin.auth')->group(function () {
	Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

	Route::middleware('permission:manage_employees')->group(function () {
		Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
		Route::get('/employees/left', [EmployeeController::class, 'left'])->name('employees.left');
		Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
		Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
		Route::patch('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
		Route::patch('/employees/{employee}/left', [EmployeeController::class, 'markLeft'])->name('employees.mark-left');
		Route::patch('/employees/{employee}/reactivate', [EmployeeController::class, 'reactivate'])->name('employees.reactivate');
	});

	Route::middleware('permission:view_employee_profiles')->group(function () {
		Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
		Route::get('/employees/{employee}/export-history', [EmployeeController::class, 'exportHistory'])->name('employees.export-history');
	});

	Route::middleware('permission:manage_attendance')->group(function () {
		Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
		Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
	});

	Route::middleware('permission:manage_payouts')->group(function () {
		Route::get('/payouts', [PayoutController::class, 'create'])->name('payouts.create');
		Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
	});

	Route::get('/reports', [ReportsController::class, 'index'])
		->middleware('permission:view_reports')
		->name('reports.index');

	Route::middleware('admin.only')->group(function () {
		Route::get('/admin/profile', [AdminUserController::class, 'profile'])->name('admin.profile');
		Route::get('/admin/profile/edit', [AdminUserController::class, 'editProfile'])->name('admin.edit-profile');
		Route::post('/admin/profile', [AdminUserController::class, 'updateProfile'])->name('admin.update-profile');

		Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
		Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
		Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
		Route::patch('/admin/users/{user}/toggle-status', [AdminUserController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');

		Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
		Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
	});
});
