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
        Schema::create('salary_grades', function (Blueprint $table) {
            $table->id();
            $table->integer('grade'); // Salary Grade number (1-33)
            $table->integer('step'); // Step number (1-8)
            $table->decimal('amount', 10, 2); // Salary amount for this grade and step
            $table->date('effective_date'); // When this salary schedule becomes effective
            $table->string('tranche')->nullable(); // e.g., "Second Tranche", "Third Tranche"
            $table->text('remarks')->nullable(); // Additional notes about this salary schedule
            $table->boolean('is_active')->default(true); // Whether this schedule is currently active
            $table->timestamps();

            // Composite unique index: one grade+step+effective_date combination
            $table->unique(['grade', 'step', 'effective_date'], 'grade_step_date_unique');

            // Index for quick lookups
            $table->index(['grade', 'step', 'is_active']);
            $table->index('effective_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_grades');
    }
};
