<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_biometric_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('type');       // fingerprint, rfid, face, etc.
            $table->string('status');     // success, failed, error, etc.
            $table->timestamp('timestamp'); // when the scan happened
            $table->unsignedBigInteger('device_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('tbl_employee')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_biometric_logs');
    }
};
