<?php

use App\Http\Controllers\DeviceCommandController;
use App\Http\Controllers\IClockController;
use App\Http\Controllers\ZKTecoDeviceController;
use App\Http\Controllers\ZKTecoUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix("zk-teco")->group(function () {
    // Route::apiResource("device", ZKTecoDeviceController::class);
    Route::prefix("device")->group(function () {
        Route::post("/action", [ZKTecoDeviceController::class, "deviceAction"]);
        Route::get("", [ZKTecoDeviceController::class, "index"]);
        Route::post("", [ZKTecoDeviceController::class, "store"]);
        Route::get("/{device_sn}", [ZKTecoDeviceController::class, "show"]);
        Route::post("/{device_sn}/action", [ZKTecoDeviceController::class, "action"]);
        Route::put("/{device_sn}", [ZKTecoDeviceController::class, "update"]);
        Route::delete("/{device_sn}", [ZKTecoDeviceController::class, "destroy"]);

        Route::prefix("/{device_sn}")->group(function () {
            Route::apiResource("user", ZKTecoUserController::class);
        });
    });
    Route::apiResource("user", ZKTecoUserController::class);
});
