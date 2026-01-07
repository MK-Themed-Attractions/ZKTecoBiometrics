<?php

namespace App\Providers;

use App\Models\ZKTeco\ZKTecoDevice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        #region ZKTeco Device
        Route::bind('device_sn', function ($device_sn) {
            $relations = request()->query('includes', []);
            $device = ZKTecoDevice::withRelations($relations)->where('device_sn', $device_sn)->first();
            if (!$device) {
                abort(response()->json([
                    'message' => 'Biometric Device not found.',
                    "data" => [
                        "device_sn" => $device_sn
                    ]
                ], 422));
            }
            return $device;
        });
        #endregion
    }
}
