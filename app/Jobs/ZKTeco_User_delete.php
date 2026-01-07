<?php

namespace App\Jobs;

use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Rats\Zkteco\Lib\ZKTeco;

class ZKTeco_User_delete implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $device_sn,
        protected string $user_uuid,

    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $zkTecoDevice = ZKTecoDevice::where("device_sn", $this->device_sn)->first();
        $zKTecoUser = ZKTecoUser::find($this->user_uuid);
        $zk = new ZKTeco($zkTecoDevice->device_ip, $zkTecoDevice->device_port);
        $zk->connect();
        $zk->removeUser($zKTecoUser->device_user_uuid);
        $zk->disconnect();
        $zKTecoUser->delete();
    }
}
