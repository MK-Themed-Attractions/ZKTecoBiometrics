<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZKTecoUserRequestValidation;
use App\Http\Resources\ZKTecoUserResource;
use App\Jobs\ZKTeco_User_delete;
use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use App\Services\ZKTecoService;
use Illuminate\Http\Request;

class ZKTecoUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ZKTecoDevice $zKTecoDevice, Request $request)
    {
        $per_page  = $request->has('per_page') ? $request->per_page : 30;
        $relations = $request->includes ?? [];
        $users = ZKTecoUser::where('device_sn', $zKTecoDevice->device_sn)->withRelations($relations);
        if ($request->has('q')) {
            $users = $users->SearchByQuery(
                $request->q ?? ""
            );
        }
        if ($request->has("filters")) {
            $users = $users->SearchByFilters($request->filters ?? []);
        }
        return ZKTecoUserResource::collection($users->simplePaginate($per_page));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ZKTecoDevice $zKTecoDevice, ZKTecoUserRequestValidation $request)
    {
        $zKTecoService = new ZKTecoService();
        $zKTecoService->createUser(
            $zKTecoDevice,
            $request->device_user_id,
            $request->device_user_name,
            $request->device_user_uuid
        );
        return response(
            [
                "message" => "ZKTeco user is added to the device with Serial Nummber: $zKTecoDevice->device_sn",
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ZKTecoUser $zKTecoDevice)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ZKTecoUser $ZKTecoUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ZKTecoDevice $device, ZKTecoUser $user, Request $request)
    {
        if ($user->device_sn != $device->device_sn) {
            return response(
                [
                    "message" => "No user found in the device"
                ],
                404
            );
        }
        ZKTeco_User_delete::dispatch($device->device_sn, $user->id);
        // $user->delete();
        return response(
            [
                "message" => "User is now being deleted in the device"
            ],
            200
        );
    }
}
