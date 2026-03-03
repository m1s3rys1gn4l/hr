<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $twoDaysAgo = now()->subDays(2)->toDateString();

        $employees = Employee::active()->get()->keyBy('id');

        foreach ($employees as $emp) {
            $fullAmount = (float) $emp->daily_salary;
            $halfAmount = $fullAmount / 2;

            AttendanceRecord::create([
                'employee_id' => $emp->id,
                'attendance_date' => $today,
                'attendance_type' => 'full',
                'overtime_hours' => 0,
                'base_amount' => $fullAmount,
                'overtime_amount' => 0,
                'credited_amount' => $fullAmount,
            ]);

            $emp->increment('current_balance', $fullAmount);
        }

        foreach ($employees->slice(0, 3) as $emp) {
            $halfAmount = (float) $emp->daily_salary / 2;

            AttendanceRecord::create([
                'employee_id' => $emp->id,
                'attendance_date' => $yesterday,
                'attendance_type' => 'half',
                'overtime_hours' => 0,
                'base_amount' => $halfAmount,
                'overtime_amount' => 0,
                'credited_amount' => $halfAmount,
            ]);

            $emp->increment('current_balance', $halfAmount);
        }

        foreach ($employees->slice(2, 2) as $emp) {
            $fullAmount = (float) $emp->daily_salary;
            $overtimeHours = 2;
            $overtimeAmount = $overtimeHours * 10.00;
            $totalCredit = $fullAmount + $overtimeAmount;

            AttendanceRecord::create([
                'employee_id' => $emp->id,
                'attendance_date' => $twoDaysAgo,
                'attendance_type' => 'full',
                'overtime_hours' => $overtimeHours,
                'base_amount' => $fullAmount,
                'overtime_amount' => $overtimeAmount,
                'credited_amount' => $totalCredit,
            ]);

            $emp->increment('current_balance', $totalCredit);
        }
    }
}
