<?php

namespace App\Models\HRIS;

use MongoDB\Laravel\Eloquent\Model;

class AttendanceSummary extends Model
{
    protected $connection = 'hris_db';

    protected $table = 'attendance_summary';

    protected $collection = 'attendance_summary';

    protected $fillable = [
        'employee_id',
        'hr_worked',
        'status',
        'late',
        'early_out',
        'date',
    ];
}
