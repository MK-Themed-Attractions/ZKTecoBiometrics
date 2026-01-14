<?php

namespace App\Console\Commands;

use App\Models\HRIS\Attendance;
use App\Models\HRIS\AttendanceSummary;
use App\Models\HRIS\Employee;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;
use Throwable;

class AttandendanceSummaryGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:attandendance-summary-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $attendance_service = new AttendanceService;
            $employees = Employee::all();

            foreach ($employees as $employee) {
                $attendances = Attendance::where('device_user_id', $employee->id_number)->whereToday('timestamp')->get();
                $summary = $attendance_service->summarizeAttendance($attendances);

                $attendance_summary = new AttendanceSummary;
                $attendance_summary->device_user_id = $employee->id_number;
                $attendance_summary->hr_worked = $summary->hr_worked;
                $attendance_summary->status = $summary->status;
                $attendance_summary->late = $summary->late;
                $attendance_summary->early_out = $summary->early_out;
                $attendance_summary->date = Carbon::today();
                $attendance_summary->save();
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
