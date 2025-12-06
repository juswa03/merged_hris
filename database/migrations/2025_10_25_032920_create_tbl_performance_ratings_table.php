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
        Schema::create('tbl_performance_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_review_id')->constrained('tbl_performance_reviews')->onDelete('cascade');
            $table->foreignId('performance_criteria_id')->constrained('tbl_performance_criteria')->onDelete('cascade');
            $table->integer('rating'); // 1-5 rating scale
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_performance_ratings');
    }
};
