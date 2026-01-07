<?php

namespace App\Services;

use App\Jobs\ZKTeco_GeneralCommand;
use App\Jobs\ZKTeco_User_add;
use App\Jobs\ZKTeco_User_get;
use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use Illuminate\Http\Response;

class ZKTecoService
{
    public function createUser(
        ZKTecoDevice | string $zkTecoDevice,
        string $device_user_id,
        string $device_user_name,
        int $device_user_uuid
    ) {
        if (is_string($zkTecoDevice)) {
            $zkTecoDevice = ZKTecoDevice::where("device_sn", $zkTecoDevice)->first();
        }
        $zKTecoUser = ZKTecoUser::create(
            [
                "device_sn" => $zkTecoDevice->device_sn,
                "device_user_id" => $device_user_id,
                "device_user_name" => $device_user_name,
                "device_user_uuid" => $device_user_uuid
            ]
        );
        ZKTeco_User_add::dispatch($zkTecoDevice->device_sn, $zKTecoUser->id);
    }
    public static function runCommand(
        ZKTecoDevice $device,
        string $command
    ): Response {
        $response = response(["message" => "Command has been sent to the Server and to the device"], 200);
        if (
            in_array($command, [
                'restart',
                'test_voice',
                'turn_off',
                'voice_test',
            ])
        ) {
            ZKTeco_GeneralCommand::dispatch($device->id, $command);
        } else {
            switch ($command) {
                case 'get_users':
                    ZKTeco_User_get::dispatch($device->device_sn);
                    break;
                default:
                    $response = response(["message" => "Invalid Command"], 404);
                    break;
            }
        }
        return $response;
    }
}
