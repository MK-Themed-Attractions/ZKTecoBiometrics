<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceService
{
    public function summarizeAttendance($attendances)
    {
        $hr_worked = 0;
        $first_in = [];
        $last_in = [];
        $last_out = [];
        $final_out = [];
        $status = 'Absent';
        $late = 0;
        $early_out = 0;
        $lateTime = Carbon::today()->setTime(7, 5, 0);
        $time_in = Carbon::today()->setTime(7, 0, 0);
        $time_out = Carbon::today()->setTime(16, 0, 0);

        foreach ($attendances as $index => $attendance) {
            if ($index == 0) {
                $first_in = $attendance;
            }

            if ($index == count($attendances) - 1) {
                $final_out = $attendance;
            }

            if ($attendance->type == 'In') {
                $last_in = $attendance;
            }

            if ($attendance->type == 'Out') {
                $last_out = $attendance;
            }

            if ($last_in && $last_out) {
                $hr_worked += $last_in->timestamp->diffInHours($last_out->timestamp);
                $status = 'Present';
                $last_in = [];
                $last_out = [];
            }
        }

        if ($status == 'Absent' && (empty($last_in) || empty($last_out))) {
            if ($last_in) {
                $status = 'No Time out';
            }

            if ($last_out) {
                $status = 'No Time in';
            }
        }

        if ($first_in && $first_in->timestamp->greaterThan($lateTime)) {
            $late = $time_in->diffInMinutes($first_in->timestamp);
            $status = 'Late';
        }

        if ($final_out && $final_out->timestamp->lessThan($time_out)) {
            $early_out = $final_out->timestamp->diffInHours($time_out);
            $status = 'Early Out';
        }

        return (object) [
            'hr_worked' => $hr_worked,
            'status' => $status,
            'late' => $late,
            'early_out' => $early_out,
        ];
    }
}
