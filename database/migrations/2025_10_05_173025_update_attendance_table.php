<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('tbl_attendance');
        Schema::create('tbl_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('attendance_source_id');
            $table->unsignedBigInteger('attendance_type_id');
            $table->string('device_uid')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('tbl_employee');
            $table->foreign('attendance_source_id')->references('id')->on('tbl_attendance_sources');
            $table->foreign('attendance_type_id')->references('id')->on('tbl_attendance_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_attendance');
    }
};