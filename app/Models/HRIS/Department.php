<?php

namespace App\Models\HRIS;

use MongoDB\Laravel\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'hris_db';

    protected $table = 'departments';

    protected $collection = 'departments';

    protected $fillable = [
        'name',
        'description',
        'biometrics',
    ];
}
