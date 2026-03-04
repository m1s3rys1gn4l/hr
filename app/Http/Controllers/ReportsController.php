<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Payout;

class ReportsController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $activeEmployees = Employee::active()->count();
        $leftEmployees = Employee::left()->count();
        $totalBalance = Employee::sum('current_balance');

        $todayAttendanceCredit = AttendanceRecord::whereDate('attendance_date', $today)
            ->sum('credited_amount');

        $monthAttendanceCredit = AttendanceRecord::whereBetween('attendance_date', [$monthStart, $monthEnd])
            ->sum('credited_amount');

        $todayPayout = Payout::whereDate('paid_at', $today)->sum('amount');
        $monthPayout = Payout::whereBetween('paid_at', [$monthStart, $monthEnd])->sum('amount');
        $totalPayout = Payout::sum('amount');

        $recentAttendance = AttendanceRecord::query()
            ->with('employee')
            ->latest('attendance_date')
            ->paginate(10);

        $recentPayouts = Payout::query()
            ->with('employee')
            ->latest('paid_at')
            ->paginate(10);

        return view('reports.index', [
            'today' => $today,
            'monthLabel' => now()->format('F Y'),
            'activeEmployees' => $activeEmployees,
            'leftEmployees' => $leftEmployees,
            'totalBalance' => $totalBalance,
            'todayAttendanceCredit' => $todayAttendanceCredit,
            'monthAttendanceCredit' => $monthAttendanceCredit,
            'todayPayout' => $todayPayout,
            'monthPayout' => $monthPayout,
            'totalPayout' => $totalPayout,
            'recentAttendance' => $recentAttendance,
            'recentPayouts' => $recentPayouts,
        ]);
    }
}
