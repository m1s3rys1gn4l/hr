<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    public function create(Request $request)
    {
        $employees = [];
        $searchError = null;

        if ($request->filled('employee_codes')) {
            $input = trim((string) $request->input('employee_codes'));
            $codes = $this->parseEmployeeCodes($input);

            if (empty($codes)) {
                $searchError = 'No valid employee codes entered.';
            } else {
                $found = Employee::active()
                    ->whereIn('employee_code', $codes)
                    ->orderBy('employee_code')
                    ->get();

                $foundCodes = $found->pluck('employee_code')->toArray();
                $notFound = array_diff($codes, $foundCodes);

                if (! empty($notFound)) {
                    $searchError = 'Employee IDs not found: ' . implode(', ', $notFound);
                }

                $employees = $found->all();
            }
        }

        return view('payouts.create', [
            'employees' => $employees,
            'searchError' => $searchError,
        ]);
    }

    private function parseEmployeeCodes(string $input): array
    {
        $parts = preg_split('/[\s,|+]+/', $input, -1, PREG_SPLIT_NO_EMPTY);
        $codes = [];

        foreach ($parts as $part) {
            if (ctype_digit($part)) {
                $codes[] = (int) $part;
            }
        }

        return array_unique($codes);
    }

    public function store(Request $request)
    {
        $returnTo = (string) $request->input('return_to', '');

        $validated = $request->validate([
            'payouts' => ['required', 'array', 'min:1'],
            'payouts.*.employee_id' => ['required', 'exists:employees,id'],
            'payouts.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payouts.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $errors = [];
        $successCount = 0;
        $employeeCodes = [];

        DB::transaction(function () use ($validated, &$errors, &$successCount, &$employeeCodes) {
            foreach ($validated['payouts'] as $index => $payoutData) {
                $employee = Employee::find($payoutData['employee_id']);
                
                if (! $employee) {
                    $errors[] = "Employee ID {$payoutData['employee_id']} not found.";
                    continue;
                }

                $employeeCodes[] = $employee->employee_code;
                $amount = (float) $payoutData['amount'];

                Payout::create([
                    'employee_id' => $employee->id,
                    'amount' => $amount,
                    'paid_at' => now(),
                    'note' => $payoutData['note'] ?? null,
                ]);

                $employee->decrement('current_balance', $amount);
                $successCount++;
            }
        });

        if (! empty($errors)) {
            return back()->withErrors(['bulk' => $errors])->withInput();
        }

        if ($returnTo !== '') {
            return redirect()->to($returnTo)
                ->with('status', "Successfully processed {$successCount} payout(s).");
        }

        $codesParam = implode('+', $employeeCodes);
        return redirect()->route('payouts.create', ['employee_codes' => $codesParam])
            ->with('status', "Successfully processed {$successCount} payout(s).");
    }
}
