<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('deduction_type_id');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('deduction_type_id')->references('id')->on('tbl_deduction_type');

        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_deductions');
    }
};