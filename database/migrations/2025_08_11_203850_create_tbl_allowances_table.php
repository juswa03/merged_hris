<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_allowances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->string('type');
            $table->unsignedBigInteger('time_stamp_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_allowances');
    }
};