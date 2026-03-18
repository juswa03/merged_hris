<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 3. Educational Background
        Schema::create('educational_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('level'); // Elementary, High School, Bachelor, Master, Doctorate, etc.
            $table->string('school_name');
            $table->string('course')->nullable();
            $table->string('period_from')->nullable();
            $table->string('period_to')->nullable();
            $table->string('year_graduated')->nullable();
            $table->string('highest_level')->nullable();
            $table->text('honors')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 4. Civil Service Eligibility
        Schema::create('civil_service_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('eligibility');
            $table->string('rating')->nullable();
            $table->date('date_of_examination')->nullable();
            $table->string('place_of_examination')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_validity')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 5. Work Experience
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('company_name');
            $table->string('position');
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->boolean('is_currently_employed')->default(false);
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('appointment_status')->nullable();
            $table->text('reason_for_resignation')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
        Schema::dropIfExists('civil_service_eligibilities');
        Schema::dropIfExists('educational_backgrounds');
    }
};
