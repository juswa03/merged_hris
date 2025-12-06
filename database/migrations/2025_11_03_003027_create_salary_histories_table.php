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
        Schema::create('salary_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->decimal('old_salary', 10, 2)->nullable();
            $table->decimal('new_salary', 10, 2);
            $table->integer('old_salary_grade')->nullable();
            $table->integer('old_salary_step')->nullable();
            $table->integer('new_salary_grade')->nullable();
            $table->integer('new_salary_step')->nullable();
            $table->enum('change_type', ['merit_increase', 'promotion', 'adjustment', 'grade_change', 'annual_increment', 'bulk_adjustment', 'initial_salary'])->default('adjustment');
            $table->text('change_reason')->nullable();
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->date('effective_date');
            $table->timestamps();

            // Indexes for better query performance
            $table->index('employee_id');
            $table->index('changed_by_user_id');
            $table->index('effective_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_histories');
    }
};
