<?php

namespace App\Models\HRIS;

use MongoDB\Laravel\Eloquent\Model;

class Deparment extends Model
{
    protected $connection = "hris_db";
    protected $table = "deparments";
    protected $collection = "deparments";
    /*
        Collection Fields / Columns
        name                str
        description         str
    */
}
