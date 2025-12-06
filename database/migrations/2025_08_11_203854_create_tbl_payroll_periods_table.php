<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('cut_off_type_id');
            $table->enum('status', ['draft', 'finalized', 'paid'])->default('draft');
            $table->timestamps();

            $table->foreign('cut_off_type_id')->references('id')->on('tbl_cutoff_types');

        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_payroll_periods');
    }
};