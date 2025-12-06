<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dtr_certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('monthly_year');
            $table->integer('regular_days');
            $table->integer('saturdays');
            $table->string('certified_by_name');
            $table->string('certified_by_position');
            $table->date('certified_at');
            $table->string('acknowledged_by_name');
            $table->string('acknowledged_by_position');
            $table->date('acknowledged_at');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('tbl_employee');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dtr_certifications');
    }
};