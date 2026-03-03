<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminAuthController;
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

	Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
	Route::get('/employees/left', [EmployeeController::class, 'left'])->name('employees.left');
	Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
	Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
	Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
	Route::patch('/employees/{employee}/left', [EmployeeController::class, 'markLeft'])->name('employees.mark-left');
	Route::patch('/employees/{employee}/reactivate', [EmployeeController::class, 'reactivate'])->name('employees.reactivate');

	Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
	Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

	Route::get('/payouts', [PayoutController::class, 'create'])->name('payouts.create');
	Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');

	Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
	Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
	Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
