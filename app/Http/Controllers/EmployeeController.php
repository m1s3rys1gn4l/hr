<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $perPage = (string) $request->query('per_page', '50');

        if (! in_array($perPage, ['50', '100', 'all'], true)) {
            $perPage = '50';
        }

        $activeQuery = Employee::active()->orderBy('employee_code');

        if ($search !== '') {
            $activeQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $query->orWhere('employee_code', (int) $search);
                }
            });
        }

        $activeEmployees = $perPage === 'all'
            ? $activeQuery->get()
            : $activeQuery->paginate((int) $perPage)->withQueryString();

        return view('employees.index', [
            'activeEmployees' => $activeEmployees,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function left(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $perPage = (string) $request->query('per_page', '50');

        if (! in_array($perPage, ['50', '100', 'all'], true)) {
            $perPage = '50';
        }

        $leftQuery = Employee::left()->latest('left_at');

        if ($search !== '') {
            $leftQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $query->orWhere('released_employee_code', (int) $search);
                }
            });
        }

        $leftEmployees = $perPage === 'all'
            ? $leftQuery->get()
            : $leftQuery->paginate((int) $perPage)->withQueryString();

        return view('employees.left', [
            'leftEmployees' => $leftEmployees,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                'unique:employees,employee_code,NULL,id,status,active',
            ],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'daily_salary' => ['required', 'numeric', 'min:0'],
        ]);

        Employee::create([
            'employee_code' => $validated['employee_code'],
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'daily_salary' => $validated['daily_salary'],
            'status' => 'active',
        ]);

        return redirect()->route('employees.index')->with('status', 'Employee created successfully.');
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                'unique:employees,employee_code,' . $employee->id . ',id,status,active',
            ],
            'daily_salary' => ['required', 'numeric', 'min:0'],
        ]);

        $employee->update([
            'employee_code' => $validated['employee_code'],
            'daily_salary' => $validated['daily_salary'],
        ]);

        return back()->with('status', 'Employee updated successfully.');
    }

    public function show(Request $request, Employee $employee)
    {
        $fromDateTime = $this->parseDateTimeFilter($request->query('from_datetime'));
        $toDateTime = $this->parseDateTimeFilter($request->query('to_datetime'));

        $attendanceHistory = $employee->attendanceRecords()
            ->when($fromDateTime, fn ($query) => $query->whereDate('attendance_date', '>=', $fromDateTime->toDateString()))
            ->when($toDateTime, fn ($query) => $query->whereDate('attendance_date', '<=', $toDateTime->toDateString()))
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(20, ['*'], 'attendance_page')
            ->withQueryString();

        $payoutHistory = $employee->payouts()
            ->when($fromDateTime, fn ($query) => $query->where('paid_at', '>=', $fromDateTime))
            ->when($toDateTime, fn ($query) => $query->where('paid_at', '<=', $toDateTime))
            ->latest('paid_at')
            ->paginate(20, ['*'], 'payout_page')
            ->withQueryString();

        return view('employees.show', [
            'employee' => $employee,
            'attendanceHistory' => $attendanceHistory,
            'payoutHistory' => $payoutHistory,
            'fromDateTime' => $fromDateTime?->format('Y-m-d\TH:i'),
            'toDateTime' => $toDateTime?->format('Y-m-d\TH:i'),
        ]);
    }

    public function exportHistory(Request $request, Employee $employee)
    {
        $fromDateTime = $this->parseDateTimeFilter($request->query('from_datetime'));
        $toDateTime = $this->parseDateTimeFilter($request->query('to_datetime'));

        $attendanceHistory = $employee->attendanceRecords()
            ->when($fromDateTime, fn ($query) => $query->whereDate('attendance_date', '>=', $fromDateTime->toDateString()))
            ->when($toDateTime, fn ($query) => $query->whereDate('attendance_date', '<=', $toDateTime->toDateString()))
            ->latest('attendance_date')
            ->latest('id')
            ->get();

        $payoutHistory = $employee->payouts()
            ->latest('paid_at')
            ->get();

        $employeeCode = $employee->employee_code ?: ($employee->released_employee_code ?: $employee->id);
        $fileName = 'employee_' . $employeeCode . '_history_' . now()->format('Ymd_His') . '.xlsx';

        $tempDirectory = storage_path('app/tmp');
        if (! is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $tempFilePath = $tempDirectory . '/' . uniqid('employee_history_', true) . '.xlsx';

        $writer = new Writer();
        $writer->openToFile($tempFilePath);

        // ===== ATTENDANCE SHEET =====
        $attendanceSheet = $writer->getCurrentSheet();
        $attendanceSheet->setName('Attendance');

        $writer->addRow(Row::fromValues(['EMPLOYEE ATTENDANCE HISTORY']));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Employee Name:', $employee->name]));
        $writer->addRow(Row::fromValues(['Employee ID:', $employee->employee_code ?: 'N/A']));
        $writer->addRow(Row::fromValues(['Phone:', $employee->phone ?: 'N/A']));
        $writer->addRow(Row::fromValues(['Daily Salary:', number_format((float) $employee->daily_salary, 2) . ' SAR']));
        $writer->addRow(Row::fromValues(['Current Balance:', number_format((float) $employee->current_balance, 2) . ' SAR']));
        $writer->addRow(Row::fromValues(['Status:', ucfirst((string) $employee->status)]));
        $writer->addRow(Row::fromValues(['Export Date:', now()->format('Y-m-d H:i:s')]));
        if ($fromDateTime || $toDateTime) {
            $fromStr = $fromDateTime?->format('Y-m-d') ?: 'Start';
            $toStr = $toDateTime?->format('Y-m-d') ?: 'Today';
            $writer->addRow(Row::fromValues(['Filter Period:', $fromStr . ' to ' . $toStr]));
        }
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Total Records:', count($attendanceHistory)]));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(Row::fromValues(['Date', 'Type', 'Overtime Hours', 'Base Amount (SAR)', 'Overtime Amount (SAR)', 'Credited Amount (SAR)']));

        $totalAttendanceCredit = 0;
        $totalOvertimeHours = 0;
        $totalOvertimeAmount = 0;

        foreach ($attendanceHistory as $record) {
            $totalAttendanceCredit += (float) $record->credited_amount;
            $totalOvertimeHours += (int) $record->overtime_hours;
            $totalOvertimeAmount += (float) $record->overtime_amount;

            $writer->addRow(Row::fromValues([
                optional($record->attendance_date)->format('Y-m-d') ?: '-',
                ucfirst((string) $record->attendance_type),
                (string) $record->overtime_hours,
                number_format((float) $record->base_amount, 2, '.', ''),
                number_format((float) $record->overtime_amount, 2, '.', ''),
                number_format((float) $record->credited_amount, 2, '.', ''),
            ]));
        }

        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['TOTALS:', '', (string) $totalOvertimeHours, '', number_format($totalOvertimeAmount, 2), number_format($totalAttendanceCredit, 2)]));

        // ===== PAYOUTS SHEET =====
        $payoutSheet = $writer->addNewSheetAndMakeItCurrent();
        $payoutSheet->setName('Payouts');

        $writer->addRow(Row::fromValues(['EMPLOYEE SALARY PAYOUTS']));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Employee Name:', $employee->name]));
        $writer->addRow(Row::fromValues(['Employee ID:', $employee->employee_code ?: 'N/A']));
        $writer->addRow(Row::fromValues(['Phone:', $employee->phone ?: 'N/A']));
        $writer->addRow(Row::fromValues(['Daily Salary:', number_format((float) $employee->daily_salary, 2) . ' SAR']));
        $writer->addRow(Row::fromValues(['Current Balance:', number_format((float) $employee->current_balance, 2) . ' SAR']));
        $writer->addRow(Row::fromValues(['Status:', ucfirst((string) $employee->status)]));
        $writer->addRow(Row::fromValues(['Export Date:', now()->format('Y-m-d H:i:s')]));
        if ($fromDateTime || $toDateTime) {
            $fromStr = $fromDateTime?->format('Y-m-d H:i') ?: 'Start';
            $toStr = $toDateTime?->format('Y-m-d H:i') ?: 'Now';
            $writer->addRow(Row::fromValues(['Filter Period:', $fromStr . ' to ' . $toStr]));
        }
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['Total Records:', count($payoutHistory)]));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(Row::fromValues(['Paid At (Date & Time)', 'Amount (SAR)', 'Note']));

        $totalPayoutAmount = 0;
        
        // Display payouts in chronological order (oldest first)
        $displayPayouts = $payoutHistory->sortBy('paid_at')->values();

        foreach ($displayPayouts as $payout) {
            $totalPayoutAmount += (float) $payout->amount;

            $writer->addRow(Row::fromValues([
                optional($payout->paid_at)->format('Y-m-d H:i') ?: '-',
                number_format((float) $payout->amount, 2, '.', ''),
                (string) ($payout->note ?: ''),
            ]));
        }

        if (count($displayPayouts) > 0) {
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['TOTAL PAYOUT:', number_format($totalPayoutAmount, 2) . ' SAR']));
        }

        $writer->close();

        return response()->download(
            $tempFilePath,
            $fileName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    private function parseDateTimeFilter(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function markLeft(Employee $employee)
    {
        if ($employee->status === 'left') {
            return back()->with('status', 'Employee is already in left list.');
        }

        DB::transaction(function () use ($employee) {
            $employee->update([
                'released_employee_code' => $employee->employee_code,
                'employee_code' => null,
                'status' => 'left',
                'left_at' => now(),
            ]);
        });

        return back()->with('status', 'Employee moved to left list and ID released.');
    }

    public function reactivate(Request $request, Employee $employee)
    {
        if ($employee->status !== 'left') {
            return back()->withErrors(['status' => 'Only left employees can be reactivated.']);
        }

        $validated = $request->validate([
            'id_strategy' => ['required', 'in:previous,new'],
        ]);

        $selectedCode = null;
        $previousCode = (int) ($employee->released_employee_code ?? 0);

        if ($validated['id_strategy'] === 'previous') {
            if ($previousCode < 1 || $previousCode > 999) {
                return back()->withErrors(['id_strategy' => 'No previous ID exists for this employee.']);
            }

            $isUsed = Employee::active()->where('employee_code', $previousCode)->exists();
            if ($isUsed) {
                return back()->withErrors(['id_strategy' => 'Previous ID is currently in use. Choose new ID.']);
            }

            $selectedCode = $previousCode;
        }

        if ($validated['id_strategy'] === 'new') {
            $usedCodes = Employee::active()
                ->whereNotNull('employee_code')
                ->pluck('employee_code')
                ->map(fn ($code) => (int) $code)
                ->all();

            $lookup = array_flip($usedCodes);
            for ($candidate = 1; $candidate <= 999; $candidate++) {
                if (isset($lookup[$candidate])) {
                    continue;
                }

                if ($previousCode > 0 && $candidate === $previousCode) {
                    continue;
                }

                $selectedCode = $candidate;
                break;
            }

            if ($selectedCode === null) {
                return back()->withErrors(['id_strategy' => 'No new ID is available (1-999).']);
            }
        }

        DB::transaction(function () use ($employee, $selectedCode) {
            $employee->update([
                'employee_code' => $selectedCode,
                'status' => 'active',
                'left_at' => null,
            ]);
        });

        return redirect()->route('employees.left')->with('status', 'Employee reactivated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->status !== 'left') {
            return back()->withErrors(['status' => 'Only left employees can be deleted.']);
        }

        if (round((float) $employee->current_balance, 2) !== 0.0) {
            return back()->withErrors(['status' => 'Only left employees with zero balance can be deleted.']);
        }

        $employee->delete();

        return redirect()->route('employees.left')->with('status', 'Left employee deleted successfully.');
    }
}
