<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 10. Other Information Tables
        Schema::create('government_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('id_type'); // SSS, TIN, GSIS, PhilHealth, etc.
            $table->string('id_number');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->unique(['employee_id', 'id_type']);
        });

        Schema::create('immigration_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('status'); // Resident, Non-Resident, OFW, etc.
            $table->text('details')->nullable();
            $table->date('effective_date')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('liabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('type'); // Criminal, Administrative, Civil
            $table->text('details');
            $table->date('date_of_case')->nullable();
            $table->string('status')->nullable(); // Pending, Resolved, etc.
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('case_type');
            $table->text('case_details');
            $table->date('date_of_case');
            $table->string('status');
            $table->text('resolution')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('political_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('activity_type');
            $table->text('description');
            $table->date('date_started')->nullable();
            $table->date('date_ended')->nullable();
            $table->text('position')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('business_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('business_name');
            $table->string('nature_of_business');
            $table->text('address');
            $table->string('role')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_interests');
        Schema::dropIfExists('political_activities');
        Schema::dropIfExists('legal_cases');
        Schema::dropIfExists('liabilities');
        Schema::dropIfExists('immigration_statuses');
        Schema::dropIfExists('government_ids');
    }
};
