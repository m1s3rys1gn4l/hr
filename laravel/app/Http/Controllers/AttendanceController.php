<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function create()
    {
        return view('attendance.create', [
            'overtimeHourlyRate' => Setting::overtimeHourlyRate(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_date' => ['required', 'date'],
            'full_time_ids' => ['nullable', 'string'],
            'half_time_ids' => ['nullable', 'string'],
            'overtime_data' => ['nullable', 'string'],
        ]);

        $fullIds = $this->parseEmployeeCodeInput($validated['full_time_ids'] ?? '');
        $halfIds = $this->parseEmployeeCodeInput($validated['half_time_ids'] ?? '');
        $overtimeData = $this->parseOvertimeInput($validated['overtime_data'] ?? '');

        if ($this->hasOverlap($fullIds, $halfIds)) {
            return back()->withErrors([
                'half_time_ids' => 'An employee ID cannot be both full time and half time on the same day.',
            ])->withInput();
        }

        $allOvertimeCodes = array_keys($overtimeData);
        $allCodes = array_values(array_unique(array_merge($fullIds, $halfIds, $allOvertimeCodes)));

        if (empty($allCodes)) {
            return back()->withErrors(['full_time_ids' => 'Please enter at least one employee ID.'])->withInput();
        }

        $employees = Employee::active()
            ->whereIn('employee_code', $allCodes)
            ->get()
            ->keyBy('employee_code');

        $missingCodes = array_values(array_diff($allCodes, $employees->keys()->map(fn ($value) => (int) $value)->all()));

        if (! empty($missingCodes)) {
            $formattedMissing = implode(', ', $missingCodes);

            return back()->withErrors([
                'full_time_ids' => "These IDs are not active or not found: {$formattedMissing}",
            ])->withInput();
        }

        $attendanceDate = Carbon::parse($validated['attendance_date'])->toDateString();

        $overtimeHourlyRate = Setting::overtimeHourlyRate();

        DB::transaction(function () use ($employees, $fullIds, $halfIds, $overtimeData, $attendanceDate, $overtimeHourlyRate) {
            foreach ($employees as $code => $employee) {
                $attendanceType = 'none';
                $baseAmount = 0.00;

                if (in_array((int) $code, $fullIds, true)) {
                    $attendanceType = 'full';
                    $baseAmount = (float) $employee->daily_salary;
                } elseif (in_array((int) $code, $halfIds, true)) {
                    $attendanceType = 'half';
                    $baseAmount = (float) $employee->daily_salary / 2;
                }

                $overtimeHours = $overtimeData[(int) $code] ?? 0;
                $overtimeAmount = $overtimeHours * $overtimeHourlyRate;
                $creditedAmount = $baseAmount + $overtimeAmount;

                $record = AttendanceRecord::query()
                    ->where('employee_id', $employee->id)
                    ->whereDate('attendance_date', $attendanceDate)
                    ->first();

                if (! $record) {
                    $record = new AttendanceRecord([
                        'employee_id' => $employee->id,
                        'attendance_date' => $attendanceDate,
                    ]);
                }

                $existingCredit = (float) ($record->credited_amount ?? 0);

                $record->fill([
                    'attendance_type' => $attendanceType,
                    'overtime_hours' => $overtimeHours,
                    'base_amount' => $baseAmount,
                    'overtime_amount' => $overtimeAmount,
                    'credited_amount' => $creditedAmount,
                ]);
                $record->save();

                $difference = $creditedAmount - $existingCredit;

                if ($difference !== 0.0) {
                    $employee->increment('current_balance', $difference);
                }
            }
        });

        return redirect()->route('employees.index')->with('status', 'Attendance saved and salary balance updated.');
    }

    private function parseEmployeeCodeInput(string $value): array
    {
        $clean = trim($value);

        if ($clean === '') {
            return [];
        }

        $parts = preg_split('/[,+\s]+/', $clean);
        $codes = [];

        foreach ($parts as $part) {
            if ($part === '' || ! ctype_digit($part)) {
                continue;
            }

            $code = (int) $part;

            if ($code >= 1 && $code <= 999) {
                $codes[] = $code;
            }
        }

        return array_values(array_unique($codes));
    }

    private function hasOverlap(array $first, array $second): bool
    {
        return ! empty(array_intersect($first, $second));
    }

    private function parseOvertimeInput(string $value): array
    {
        $clean = trim($value);

        if ($clean === '') {
            return [];
        }
        
        $overtimeData = [];
        $parts = preg_split('/[\s,|+]+/', $clean);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $segments = explode(':', $part);
            if (count($segments) !== 2) {
                continue;
            }

            $code = (int) trim($segments[0]);
            $hours = (int) trim($segments[1]);

            if ($code >= 1 && $code <= 999 && $hours >= 1 && $hours <= 3) {
                $overtimeData[$code] = $hours;
            }
        }

        return $overtimeData;
    }
}
