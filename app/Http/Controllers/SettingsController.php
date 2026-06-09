<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('settings.edit', [
            'overtimeHourlyRate' => Setting::overtimeHourlyRate(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'overtime_hourly_rate' => ['required', 'numeric', 'min:0', 'max:10000'],
        ]);

        Setting::setValue('overtime_hourly_rate', (string) round((float) $validated['overtime_hourly_rate'], 2));

        return redirect()->route('settings.edit')->with('status', 'Settings updated successfully.');
    }

    public function cleanDatabase(Request $request)
    {
        $request->validate([
            'confirmation' => ['required', 'in:CLEAN'],
        ]);

        DB::transaction(function () {
            $tablesToClean = [
                'attendance_records',
                'payouts',
                'settings',
                'cache',
                'cache_locks',
                'jobs',
                'job_batches',
                'failed_jobs',
                'sessions',
                'password_reset_tokens',
            ];

            Schema::disableForeignKeyConstraints();

            try {
                foreach ($tablesToClean as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->delete();
                    }
                }

                Employee::query()->update(['current_balance' => 0]);
            } finally {
                Schema::enableForeignKeyConstraints();
            }
        });

        return redirect()->route('settings.edit')->with('status', 'Database cleaned. Users and employees were preserved.');
    }
}
