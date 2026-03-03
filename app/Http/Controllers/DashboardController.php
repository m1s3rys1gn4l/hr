<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payout;

class DashboardController extends Controller
{
    public function index()
    {
        $activeCount = Employee::active()->count();
        $leftCount = Employee::left()->count();
        $totalBalance = Employee::sum('current_balance');
        $todayPayout = Payout::whereDate('paid_at', now()->toDateString())->sum('amount');
        $recentEmployees = Employee::query()
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard', [
            'activeCount' => $activeCount,
            'leftCount' => $leftCount,
            'totalBalance' => $totalBalance,
            'todayPayout' => $todayPayout,
            'recentEmployees' => $recentEmployees,
        ]);
    }
}
