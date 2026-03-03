<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::active()->get();

        foreach ($employees as $emp) {
            if ((float) $emp->current_balance > 0) {
                $payoutAmount = (float) $emp->current_balance * 0.5;

                Payout::create([
                    'employee_id' => $emp->id,
                    'amount' => $payoutAmount,
                    'paid_at' => now()->subDay(),
                    'note' => 'Weekly advance payment',
                ]);

                $emp->decrement('current_balance', $payoutAmount);
            }
        }
    }
}
