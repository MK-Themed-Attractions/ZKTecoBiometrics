<?php

namespace App\Models\ZKTeco;

use App\Traits\MiscModelTrait;
use MongoDB\Laravel\Eloquent\Model;
use Rats\Zkteco\Lib\ZKTeco;

class ZKTecoDevice extends Model
{
    use MiscModelTrait;

    protected $connection = 'mongodb';

    protected $table = 'zk_teco_devices';

    protected $collection = 'zk_teco_devices';

    protected $fillable = [
        'device_sn',
        'device_model',
        'device_ip',
        'device_port',
        'device_loc',
        'device_name',
    ];

    protected $relations = ['users'];

    protected $hidden = [
        '_id',
        'id',
        'updated_at',
        'created_at',
    ];

    public function users()
    {
        return $this->hasMany(ZKTecoUser::class, 'device_sn', 'device_sn');
    }

    public function fetchAttendance()
    {
        $zk = new ZKTeco($this->device_ip, $this->device_port);
        $zk->connect();
        $attendances = $zk->getAttendance();
        $zk->disconnect();

        return $attendances;
    }

    public function clearAttendance()
    {
        $zk = new ZKTeco($this->device_ip, $this->device_port);
        $zk->connect();
        $zk->clearAttendance();
        $zk->disconnect();

        return true;
    }
}
