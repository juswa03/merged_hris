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
        Schema::create('travel_authorities', function (Blueprint $table) {
            $table->id();
            
            // Employee and basic info
            $table->foreignId('employee_id')->constrained('tbl_employee');
            $table->string('travel_authority_no')->unique();
            $table->string('destination');
            $table->text('purpose');
            
            // Travel details
            $table->string('travel_type'); // official_time, official_business, personal_abroad, official_travel
            $table->string('duration_type'); // single_day, multiple_days
            $table->date('start_date');
            $table->date('end_date');
            
            // Transportation and funding
            $table->string('transportation'); // university_vehicle, public_conveyance, private_vehicle
            $table->decimal('estimated_expenses', 12, 2)->nullable();
            $table->string('source_of_funds'); // mooe, personal, other
            $table->string('other_funds_specification')->nullable();
            
            // Submission and workflow
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('tbl_users');
            $table->string('status')->default('pending'); // pending, approved, rejected, completed, cancelled
            
            // Recommendation
            $table->foreignId('recommending_official_id')->nullable()->constrained('tbl_users');
            
            // Additional info
            $table->text('remarks')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('signature_path')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('travel_type');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_authorities');
    }
};
