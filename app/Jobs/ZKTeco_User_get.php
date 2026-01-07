<?php

namespace App\Jobs;

use App\Libraries\ZKLib;
use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Rats\Zkteco\Lib\ZKTeco;

class ZKTeco_User_get implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue;

    // Optional: retries and timeout
    public $tries = 3;
    public $timeout = 120;

    public function __construct(protected string $device_sn)
    {
        //
    }

    public function handle(): void
    {
        $zkTeco = ZKTecoDevice::where("device_sn", $this->device_sn)->first();
        $zk = new ZKTeco($zkTeco->device_ip, $zkTeco->device_port);
        #region
        if (!$zk->connect()) {
            Log::error("Failed to connect to device: {$zkTeco->device_sn}");
        }
        $user_list = $zk->getUser();
        $zk->disconnect();
        $user_list = collect($user_list)->map(function ($user) use ($zkTeco) {
            return [
                "device_sn"           => $zkTeco->device_sn,
                "device_user_id"      => $user["userid"],
                "device_user_name"    => $user["name"],
                "device_user_card"    => $user["cardno"],
                "device_user_uuid"    => $user["uid"],
                "device_user_role"    => $user["role"],
                "device_user_password" => $user["password"],
            ];
        })->values();
        $operations = [];
        foreach ($user_list->toArray() as $u) {
            $operations[] = [
                'updateOne' => [
                    // UNIQUE IDENTIFIER (composite key)
                    [
                        'device_sn'      => $u['device_sn'],
                        'device_user_id' => $u['device_user_id'],
                    ],
                    [
                        '$set' => $u, // update fields
                    ],
                    ['upsert' => true] // insert if not exists
                ]
            ];
        }
        if (!empty($operations)) {
            ZKTecoUser::raw(function ($collection) use ($operations) {
                return $collection->bulkWrite($operations);
            });
        }
        
        Log::info("Fetched " . $user_list->count() . " users from device {$zkTeco->device_sn}");
        #endregion

    }
}
