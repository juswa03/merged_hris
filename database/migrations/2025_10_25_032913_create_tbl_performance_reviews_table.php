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
        Schema::create('tbl_performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('tbl_users')->onDelete('cascade');
            $table->date('review_period_start');
            $table->date('review_period_end');
            $table->enum('review_type', ['quarterly', 'annual', 'probation', 'mid_year'])->default('annual');
            $table->enum('status', ['draft', 'pending', 'completed', 'approved'])->default('draft');
            $table->decimal('overall_rating', 3, 2)->nullable(); // e.g., 4.50
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('employee_comments')->nullable();
            $table->text('reviewer_comments')->nullable();
            $table->text('hr_comments')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('tbl_users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_performance_reviews');
    }
};
