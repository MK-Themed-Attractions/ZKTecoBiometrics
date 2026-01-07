<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZKTecoDeviceRequestValidation;
use App\Http\Resources\ZKTecoDeviceResource;
use App\Jobs\ZKTeco_GeneralCommand;
use App\Jobs\ZKTeco_User_get;
use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use App\Services\ZKTecoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZKTecoDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page  = $request->has('per_page') ? $request->per_page : 30;
        $relations = $request->includes ?? [];
        $devices = ZKTecoDevice::withRelations($relations);
        if ($request->has('q')) {
            $devices = $devices->SearchByQuery(
                $request->q ?? ""
            );
        }
        if ($request->has("filters")) {
            $devices = $devices->SearchByFilters($request->filters ?? []);
        }
        return ZKTecoDeviceResource::collection($devices->simplePaginate($per_page));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ZKTecoDeviceRequestValidation $request)
    {
        $zKTecoDevice = ZKTecoDevice::create(
            [
                "device_sn" => $request->device_sn,
                "device_model" => $request->device_model,
                "device_ip" => $request->device_ip,
                "device_port" => $request->device_port,
                "device_loc" => $request->device_loc,
                "device_name" => $request->device_name,
            ]
        );
        ZKTeco_User_get::dispatch($zKTecoDevice->device_sn);
        return response(
            [
                "message" => "ZK Teco Device has been registered",
                "data" => $zKTecoDevice
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ZKTecoDevice $device, Request $request)
    {
        return new ZKTecoDeviceResource($device);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(ZKTecoDeviceRequestValidation $request, ZKTecoDevice $device)
    {

        $isNewIP = in_array(
            false,
            [
                $device->device_ip == $request->device_ip,
                $device->device_port == $request->device_port,
            ]
        );
        $device->device_sn = $request->device_sn;
        $device->device_model = $request->device_model;
        $device->device_ip = $request->device_ip;
        $device->device_port = $request->device_port;
        $device->device_loc = $request->device_loc;
        $device->device_name = $request->device_name;
        $device->save();
        if ($isNewIP) {
            ZKTeco_User_get::dispatch($device);
        }
        return response(
            [
                "message" => "Device have been updated"
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ZKTecoDevice $device)
    {
        ZKTecoUser::where('device_sn', $device->device_sn)->delete();
        $device->delete();
        return response(
            [
                "message" => "Device and users has been deleted"
            ],
            200
        );
    }


    public function action(ZKTecoDevice $device, Request $request)
    {
        return ZKTecoService::runCommand($device, $request->action);
    }


    public function deviceAction(Request $request)
    {
        
        $responses = [];
        $service = new ZKTecoService();
        foreach (ZKTecoDevice::get() as $key => $device) {
            $response = $service->runCommand($device,$request->action);
            if($response->status() != 200){
                exit();
            }
        }
    }
}
