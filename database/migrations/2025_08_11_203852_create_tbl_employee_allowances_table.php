<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('allowance_id');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('tbl_employee');
            $table->foreign('allowance_id')->references('id')->on('tbl_allowances');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_employee_allowances');
    }
};