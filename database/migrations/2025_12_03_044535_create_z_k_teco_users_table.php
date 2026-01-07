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
        Schema::create('zk_teco_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->text("device_sn");  // ZKTeco Device SN
            $table->text("device_user_uuid"); // Device User Data Location

            $table->text("device_user_id"); // User ID assigned in the ZK Teco Device
            $table->text("device_user_name"); // Employee Name
            $table->text("device_user_card"); // Employee Card

            $table->text("device_user_password"); // Employee Password
            $table->text("device_user_role"); // Employee Role

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zk_teco_users');
    }
};
