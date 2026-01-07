<?php

namespace App\Models\ZKTeco;

use App\Traits\MiscModelTrait;
use MongoDB\Laravel\Eloquent\Model;

class ZKTecoUser extends Model
{

    use MiscModelTrait;
    protected $connection = "mongodb";
    protected $table = "zk_teco_users";
    protected $collection = "zk_teco_users";
    protected $fillable = [
        "device_sn",
        "device_user_id",
        "device_user_name",
        "device_user_card",
        "device_user_role",
        "device_user_password",
        "device_user_uuid"
    ];
    // protected $hidden = ["id", "_id"];
}
