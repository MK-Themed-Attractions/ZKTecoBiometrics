<?php

namespace App\Console\Commands;

use App\Models\HRIS\Department;
use App\Models\HRIS\Employee;
use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use App\Services\ZKTecoService;
use Illuminate\Console\Command;

class SyncEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-employee';

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
        $employees = Employee::all();

        foreach ($employees as $employee) {
            if (! ZKTecoUser::where('device_user_id', $employee->id_number)->exists()) {
                $department = Department::find($employee->department_id);

                if ($department) {
                    $zKTecoDevice = ZKTecoDevice::where('device_sn', $department->biometrics)->first();

                    $employee_name = $employee->first_name.' '.$employee->last_name;

                    if ($zKTecoDevice) {
                        $zKTecoService = new ZKTecoService;
                        $zKTecoService->createUser(
                            $zKTecoDevice,
                            $employee->id_number,
                            $employee_name,
                            ZKTecoUser::all()->count()
                        );
                    }
                }
            }
        }

    }
}
