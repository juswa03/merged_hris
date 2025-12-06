<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_fingerprint_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->longText('template');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('tbl_employee');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_fingerprint_templates');
    }
};