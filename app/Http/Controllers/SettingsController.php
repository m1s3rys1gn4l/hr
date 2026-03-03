<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

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
}
