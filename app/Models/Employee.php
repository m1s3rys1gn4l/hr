<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'released_employee_code',
        'name',
        'phone',
        'daily_salary',
        'current_balance',
        'status',
        'left_at',
    ];

    protected function casts(): array
    {
        return [
            'daily_salary' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'left_at' => 'datetime',
        ];
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLeft($query)
    {
        return $query->where('status', 'left');
    }

    public static function allocateNextCode(): ?int
    {
        $usedCodes = self::active()
            ->whereNotNull('employee_code')
            ->pluck('employee_code')
            ->map(fn ($code) => (int) $code)
            ->all();

        $lookup = array_flip($usedCodes);

        for ($code = 1; $code <= 999; $code++) {
            if (! isset($lookup[$code])) {
                return $code;
            }
        }

        return null;
    }

    public function formattedCode(): ?string
    {
        return $this->employee_code ? (string) $this->employee_code : null;
    }
}
