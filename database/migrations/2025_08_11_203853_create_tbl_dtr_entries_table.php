<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_dtr_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('dtr_date');
            $table->time('am_arrival')->nullable();
            $table->time('am_departure')->nullable();
            $table->time('pm_arrival')->nullable();
            $table->time('pm_departure')->nullable();
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->integer('total_minutes')->default(0);
            $table->integer('under_time_minutes')->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_weekend')->default(false);
            $table->enum('status', ['present', 'absent', 'late', 'undertime'])->default('present');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('tbl_employee');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_dtr_entries');
    }
};