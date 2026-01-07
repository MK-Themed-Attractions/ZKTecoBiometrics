<?php

namespace App\Models\HRIS;

use App\Models\ZKTeco\ZKTecoDevice;
use App\Models\ZKTeco\ZKTecoUser;
use MongoDB\Laravel\Eloquent\Model;

class Employee extends Model
{
    protected $connection = "hris_db";
    protected $table = "employees";
    protected $collection = "employees";
    /*
        Collection Fields / Columns
        address             str
        mobile_number       str
        date_of_birth       datetime
        place_of_birth      str
        civil_status        str
        gender              str
        date_hired          datetime
        id_number           str
        first_name          str
        middle_name         Optional[str] = None
        last_name           str
        position            str
        has_account         Optional[bool] = False
    */
    protected $visible = [
        "id_number",
        "first_name",
        "middle_name",
        "last_name",
        "position"
    ];
    public function deviceUsers()
    {
        return $this->hasMany(ZKTecoUser::class, 'id_number', 'device_user_id');
    }

    
}
