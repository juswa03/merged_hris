<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 12. Relatives in Government Service
        Schema::create('relatives_in_gov_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('relative_name');
            $table->string('relationship');
            $table->string('agency');
            $table->string('position');
            $table->timestamps();
            $table->index('employee_id');
        });

        // 13. Relationship to Authority
        Schema::create('relationships_to_authorities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('authority_name');
            $table->string('relationship_type');
            $table->text('details')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 14. Employment Separation
        Schema::create('employment_separations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->date('separation_date');
            $table->string('separation_type'); // Resignation, Retirement, Termination, Dismissal, etc.
            $table->text('reason');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 15. Voluntary Work or Civic Duty
        Schema::create('volunteer_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('organization_name');
            $table->string('position');
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->text('nature_of_work')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_works');
        Schema::dropIfExists('employment_separations');
        Schema::dropIfExists('relationships_to_authorities');
        Schema::dropIfExists('relatives_in_gov_services');
    }
};
