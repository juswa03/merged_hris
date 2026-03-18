<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Personal Information (belongs to User)
        Schema::create('personal_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tbl_users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->date('date_of_birth');
            $table->string('place_of_birth')->nullable();
            $table->string('citizenship')->nullable();
            $table->string('dual_citizenship_type')->nullable();
            $table->foreignId('dual_citizenship_country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('sex')->nullable();
            $table->string('civil_status')->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('blood_type')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('agency_employee_no')->nullable();
            $table->string('filing_type')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_informations');
    }
};
