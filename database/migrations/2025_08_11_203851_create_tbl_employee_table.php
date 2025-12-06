<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tbl_employee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // link to users table
            
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('birthdate');
            $table->string('contact_number');
            $table->text('address');
            $table->string('photo_url')->nullable();
            $table->date('hire_date');
            $table->date('date_resign')->nullable();
            $table->string('rfid_code')->unique()->nullable();
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed']);
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('employment_type_id');
            $table->unsignedBigInteger('job_status_id');
            $table->string('biometric_user_id')->nullable();
            $table->timestamps();
            
            // foreign keys
            $table->foreign('user_id')->references('id')->on('tbl_users')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('tbl_departments')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('tbl_positions')->onDelete('cascade');
            $table->foreign('employment_type_id')->references('id')->on('tbl_employment_type')->onDelete('cascade');
            $table->foreign('job_status_id')->references('id')->on('tbl_job_statuses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_employee');
    }
};
