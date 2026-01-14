<?php

namespace App\Models\HRIS;

use MongoDB\Laravel\Eloquent\Model;

class Attendance extends Model
{
    protected $connection = 'hris_db';

    protected $table = 'attendances';

    protected $collection = 'attendances';

    protected $fillable = [
        'uid',
        'device_user_id',
        'state',
        'timestamp',
        'type',
    ];
}
