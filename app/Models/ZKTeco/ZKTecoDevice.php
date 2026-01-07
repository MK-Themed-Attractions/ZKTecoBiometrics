<?php

namespace App\Models\ZKTeco;

use App\Traits\MiscModelTrait;
use MongoDB\Laravel\Eloquent\Model;

class ZKTecoDevice extends Model
{
    use MiscModelTrait;
    protected $connection = "mongodb";
    protected $table = "zk_teco_devices";
    protected $collection = "zk_teco_devices";

    protected $fillable = [
        "device_sn",
        "device_model",
        "device_ip",
        "device_port",
        "device_loc",
        "device_name",
    ];

    protected $relations = ["users"];
    protected $hidden = [
        "_id",
        "id",
        "updated_at",
        "created_at"
    ];

    public function users()
    {
        return $this->hasMany(ZKTecoUser::class, 'device_sn', 'device_sn');
    }
}
