<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zk_teco_devices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->text("device_sn");
            $table->text("device_model");

            $table->ipAddress("device_ip");
            $table->integer("device_port");

            $table->text("device_loc");
            $table->text("device_name");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zk_teco_devices');
    }
};
