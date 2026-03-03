<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'attendance_type',
        'overtime_hours',
        'base_amount',
        'overtime_amount',
        'credited_amount',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'overtime_hours' => 'integer',
            'base_amount' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'credited_amount' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
