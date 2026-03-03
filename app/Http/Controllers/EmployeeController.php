<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'daily_salary' => ['required', 'numeric', 'min:0'],
        ]);

        $code = Employee::allocateNextCode();

        if ($code === null) {
            return back()->withErrors(['name' => 'No employee ID is available (1-999).'])->withInput();
        }

        Employee::create([
            'employee_code' => $code,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'daily_salary' => $validated['daily_salary'],
            'status' => 'active',
        ]);

        return redirect()->route('employees.index')->with('status', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $attendanceHistory = $employee->attendanceRecords()
            ->latest('attendance_date')
            ->latest('id')
            ->paginate(15, ['*'], 'attendance_page');

        $payoutHistory = $employee->payouts()
            ->latest('paid_at')
            ->paginate(15, ['*'], 'payout_page');

        return view('employees.show', [
            'employee' => $employee,
            'attendanceHistory' => $attendanceHistory,
            'payoutHistory' => $payoutHistory,
        ]);
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
}
