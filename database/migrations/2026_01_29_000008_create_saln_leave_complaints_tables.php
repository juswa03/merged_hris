<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 16. SALN (Statement of Assets, Liabilities and Net Worth)
        Schema::create('salns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->date('filing_date');
            $table->string('year'); // Year of filing
            $table->string('period'); // Calendar Year, First Half, Second Half, etc.
            $table->decimal('total_real_properties', 15, 2)->nullable();
            $table->decimal('total_personal_properties', 15, 2)->nullable();
            $table->decimal('total_liabilities', 15, 2)->nullable();
            $table->decimal('net_worth', 15, 2)->nullable();
            $table->string('status')->default('draft'); // draft, submitted, verified, etc.
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->index('filing_date');
        });

        // 17. Leave Balance and Credit Earnings
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('leave_type'); // Vacation, Sick, etc.
            $table->decimal('opening_balance', 8, 4);
            $table->decimal('earned', 8, 4);
            $table->decimal('used', 8, 4)->default(0);
            $table->decimal('closing_balance', 8, 4);
            $table->integer('year');
            $table->timestamps();
            $table->index('employee_id');
            $table->unique(['employee_id', 'leave_type', 'year']);
        });

        Schema::create('leave_credit_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('leave_type');
            $table->decimal('credits_earned', 8, 4);
            $table->date('period_from');
            $table->date('period_to');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 18. Employee Complaints
        Schema::create('employee_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('complaint_type');
            $table->text('description');
            $table->date('date_filed');
            $table->string('status')->default('pending'); // pending, under_review, resolved, closed
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->index('status');
        });

        Schema::create('complaint_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_complaint_id')->constrained('employee_complaints')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('tbl_users');
            $table->string('status');
            $table->text('comments');
            $table->timestamp('updated_at');
            $table->index('employee_complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_updates');
        Schema::dropIfExists('employee_complaints');
        Schema::dropIfExists('leave_credit_earnings');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('salns');
    }
};
