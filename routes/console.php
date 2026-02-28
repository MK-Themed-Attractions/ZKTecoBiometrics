<?php

use App\Jobs\getUsers;
use App\Jobs\ZKTeco_User_get;
use App\Libraries\ZKLib;
use App\Models\AttendanceLog;
use App\Models\HRIS\Employee;
use App\Models\IclockCommand;
use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

// Artisan::command('test', function () {
//     Log::info(Employee::where('id_number', "MK-521")->first()->withDeviceData());
// });

Schedule::command('app:fetch-attendance')
    ->dailyAt('08:00')
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:fetch-attendance')
    ->dailyAt('12:00')
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:fetch-attendance')
    ->dailyAt('14:00')
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:fetch-attendance')
    ->dailyAt('20:00')
    ->runInBackground()
    ->withoutOverlapping()->then(function () {
        Artisan::call('app:attandendance-summary-generate');
    });

Schedule::command('app:sync-employee')
    ->everyTenMinutes()
    ->runInBackground()
    ->withoutOverlapping();