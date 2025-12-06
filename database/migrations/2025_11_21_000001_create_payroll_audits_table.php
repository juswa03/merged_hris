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
        if (!Schema::hasTable('tbl_payroll_audits')) {
            Schema::create('tbl_payroll_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')->constrained('tbl_payrolls')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('action', ['created', 'updated', 'approved', 'rejected', 'processed', 'paid', 'deleted', 'regenerated']);
                $table->json('changes')->nullable()->comment('Before/after values of changed fields');
                $table->text('reason')->nullable()->comment('Reason for the action');
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
                
                // Indexes for efficient queries
                $table->index('payroll_id');
                $table->index('user_id');
                $table->index('action');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_payroll_audits');
    }
};
