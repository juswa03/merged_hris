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
        Schema::create('leave', function (Blueprint $table) {
            $table->id();
            
            // Employee & basic info
            $table->foreignId('employee_id')->constrained('tbl_employee');
            $table->foreignId('department_id')->nullable()->constrained('tbl_departments');
            $table->foreignId('position_id')->nullable()->constrained('tbl_positions');
            $table->date('filing_date');
            $table->string('type'); // vacation, sick, maternity, paternity, etc.
            
            // CSC-specific fields
            $table->string('csc_employee_type')->nullable(); // regular, teacher, part-time, contractual, etc.
            $table->string('leave_basis')->nullable(); // standard_vl_sl, teacher_pvp, special_law, part_time_proportional
            $table->boolean('is_vacation_service')->default(false);
            $table->decimal('service_credits_used', 8, 4)->nullable();
            
            // Duration fields
            $table->date('start_date');
            $table->date('end_date');
            $table->string('duration_type'); // full_day, half_day, multiple_days
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('half_day_time')->nullable(); // morning, afternoon
            
            // Special leave conditions
            $table->date('maternity_delivery_date')->nullable();
            $table->integer('paternity_delivery_count')->nullable();
            $table->boolean('is_miscarriage')->default(false);
            $table->string('slp_type')->default('none'); // special_privilege_leave_type
            
            // Leave for without pay
            $table->boolean('is_lwop')->default(false);
            $table->boolean('is_monetized')->default(false);
            $table->decimal('monetized_days', 8, 4)->nullable();
            $table->decimal('monetization_amount', 12, 2)->nullable();
            
            // Terminal & forced leave
            $table->boolean('is_forced_leave')->default(false);
            $table->boolean('is_terminal_leave')->default(false);
            $table->string('separation_type')->nullable(); // retirement, voluntary_resignation, separation_no_fault, none
            
            // Medical and fitness
            $table->date('medical_certificate_issued_date')->nullable();
            $table->boolean('is_fit_to_work')->nullable();
            $table->decimal('actual_service_days', 8, 4)->nullable();
            $table->boolean('included_in_service')->nullable();
            
            // Computation
            $table->string('computation_method')->nullable();
            $table->text('computation_notes')->nullable();
            
            // Documents
            $table->string('commutation')->nullable(); // requested, not_requested
            $table->text('reason')->nullable();
            $table->longText('signature_data')->nullable();
            $table->string('electronic_signature_path')->nullable();
            $table->string('medical_certificate_path')->nullable();
            $table->string('travel_itinerary_path')->nullable();
            
            // Leave credits
            $table->date('credit_as_of_date')->nullable();
            $table->decimal('vacation_earned', 8, 4)->nullable();
            $table->decimal('vacation_less', 8, 4)->nullable();
            $table->decimal('vacation_balance', 8, 4)->nullable();
            $table->decimal('sick_earned', 8, 4)->nullable();
            $table->decimal('sick_less', 8, 4)->nullable();
            $table->decimal('sick_balance', 8, 4)->nullable();
            
            // Approval
            $table->string('recommendation')->nullable(); // approve, disapprove
            $table->string('approved_for')->nullable(); // with_pay, without_pay, others
            $table->decimal('with_pay_days', 8, 4)->nullable();
            $table->decimal('without_pay_days', 8, 4)->nullable();
            $table->string('others_specify')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Workflow - Simplified
            $table->string('workflow_status')->default('pending'); 
            // pending, dept_recommended, dept_rejected, hr_certified, 
            // president_approved, president_rejected, approved, rejected
            
            // Certifications and recommendations
            $table->foreignId('certified_by')->nullable()->constrained('tbl_users');
            $table->timestamp('certified_at')->nullable();
            $table->foreignId('recommended_by')->nullable()->constrained('tbl_users');
            $table->timestamp('recommended_at')->nullable();
            $table->foreignId('approved_by_president')->nullable()->constrained('tbl_users');
            $table->timestamp('approved_by_president_at')->nullable();
            
            // Final approval
            $table->foreignId('approved_by')->nullable()->constrained('tbl_users');
            $table->timestamp('approved_at')->nullable();
            
            // Status and additional info
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->foreignId('handover_person_id')->nullable()->constrained('tbl_users');
            $table->text('handover_notes')->nullable();
            
            // Leave type specific details (JSON)
            $table->json('leave_type_details')->nullable();
            
            // PDF
            $table->string('pdf_path')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('employee_id');
            $table->index('workflow_status');
            $table->index('status');
            $table->index('type');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave');
    }
};
