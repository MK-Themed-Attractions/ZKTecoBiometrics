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

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    Log::info(Employee::where('id_number', "MK-521")->first()->withDeviceData());
});
