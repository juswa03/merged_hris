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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();  
            $table->string('building');            
            $table->string('floor');            
            
            // Foreign key to departments table
            $table->unsignedBigInteger('department_id');  
            $table->foreign('department_id')
                  ->references('id')
                  ->on('tbl_departments')
                  ->onDelete('cascade');

            $table->string('room');                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
