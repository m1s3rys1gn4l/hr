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

        // Get date range from request
        $fromDate = request('from_date', $monthStart);
        $toDate = request('to_date', $today);

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

        // Date-wise filtered attendance
        $recentAttendance = AttendanceRecord::query()
            ->with('employee')
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->latest('attendance_date')
            ->paginate(20);

        // Date-wise filtered payouts
        $recentPayouts = Payout::query()
            ->with('employee')
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->latest('paid_at')
            ->paginate(20);

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
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }
}
