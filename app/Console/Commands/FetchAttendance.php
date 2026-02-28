<?php

namespace App\Console\Commands;

use App\Models\HRIS\Attendance;
use App\Models\ZKTeco\ZKTecoDevice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;
use Log;

class FetchAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-attendance';

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
            $devices = ZKTecoDevice::all();
            foreach ($devices as $device) {
                $attendances = $device->fetchAttendance();
                foreach ($attendances as $attendance) {
                    $last = Attendance::where('device_user_id', $attendance['id'])->whereToday('timestamp')->latest()->first();

                    $status = 'In';
                    if ($last && $last->type == 'In') {
                        $status = 'Out';

                        // if ($last->type == 'Out' && Carbon::now()->gt(Carbon::today()->setTime(11, 0))) {
                        //     $status = 'Lunch Break';
                        // }
                    }

                    if (! $last || $last->timestamp->diffInMinutes(Carbon::parse($attendance['timestamp'])) >= 1) {
                        $attendance_data = new Attendance;
                        $attendance_data->uid = $attendance['uid'];
                        $attendance_data->device_user_id = $attendance['id'];
                        $attendance_data->state = $attendance['state'];
                        $attendance_data->timestamp = Carbon::parse($attendance['timestamp']);
                        $attendance_data->type = $status;
                        $attendance_data->save();
                    }
                }

                $device->clearAttendance();
            }
        } catch (Throwable $e) {
            \Log::error($e->getMessage());
        }
    }
}
