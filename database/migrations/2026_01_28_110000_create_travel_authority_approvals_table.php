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
        // Drop existing table if it exists
        Schema::dropIfExists('travel_authority_approvals');
        
        Schema::create('travel_authority_approvals', function (Blueprint $table) {
            $table->id();
            
            // Foreign key
            $table->foreignId('travel_authority_id')->constrained('travel_authorities')->onDelete('cascade');
            
            // Approval details
            $table->string('approval_type'); // finance_officer_approval, accountant_approval, dept_head_approval, president_approval
            $table->integer('order')->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected
            
            // Approver info
            $table->foreignId('approved_by')->nullable()->constrained('tbl_users');
            $table->string('approver_role')->nullable();
            
            // Approval dates
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // Comments and signatures
            $table->text('comments')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('signature_path')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('travel_authority_id');
            $table->index('approval_type');
            $table->index('status');
            $table->unique(['travel_authority_id', 'approval_type'], 'ta_ta_id_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_authority_approvals');
    }
};
