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
        if (!Schema::hasTable('tbl_payroll_approvals')) {
            Schema::create('tbl_payroll_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_period_id')->constrained('tbl_payroll_periods')->onDelete('cascade');
                // Use unsignedBigInteger explicitly to be safe, though foreignId does this.
                // If users table uses integer, this might fail.
                $table->foreignId('approver_id')->constrained('users')->onDelete('restrict');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->unique(['payroll_period_id', 'approver_id']);
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_payroll_approvals');
    }
};
