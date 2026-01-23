<?php

namespace App\Models\HRIS;

use MongoDB\Laravel\Eloquent\Model;

class AttendanceSummary extends Model
{
    protected $connection = 'hris_db';

    protected $table = 'attendance_summaries';

    protected $collection = 'attendance_summaries';

    protected $fillable = [
        'employee_id',
        'hr_worked',
        'status',
        'late',
        'early_out',
        'date',
    ];
}
