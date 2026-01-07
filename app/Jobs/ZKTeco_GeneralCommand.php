<?php

namespace App\Jobs;

use App\Models\ZKTeco\ZKTecoDevice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Rats\Zkteco\Lib\ZKTeco;

class ZKTeco_GeneralCommand implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $device_sn, public string $command)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $zkTecoDevice = ZKTecoDevice::where("device_sn", $this->device_sn)->first();

        $zk = new ZKTeco($zkTecoDevice->device_ip, $zkTecoDevice->device_port);
        $zk->connect();
        switch ($this->command) {
            case 'restart':
                $zk->restart();
                break;
            case 'test_voice':
                $zk->restart();
                break;
            case 'turn_off':
                $zk->shutdown();
                break;
            case 'voice_test':
                $zk->testVoice();
                break;
        }
        $zk->disconnect();
    }
}
