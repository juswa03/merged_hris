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
        Schema::table('tbl_employee', function (Blueprint $table) {
            $table->boolean('is_pwd')->default(false)->nullable();
            $table->string('program')->nullable();
            $table->string('highest_educational_attainment')->nullable();
            $table->string('employee_category')->nullable();
            $table->string('work_schedule')->nullable();
            $table->string('provider')->nullable();
            $table->decimal('work_hours_per_week', 5, 2)->nullable();
            $table->decimal('work_hours_per_day', 5, 2)->nullable();
            $table->boolean('is_teacher')->default(false)->nullable();
            $table->decimal('vacation_service_credits', 8, 2)->default(0)->nullable();
            $table->string('marital_status')->nullable();
            $table->date('last_delivery_date')->nullable();
            $table->date('last_leave_computation_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_employee', function (Blueprint $table) {
            $table->dropColumn([
                'is_pwd',
                'program',
                'highest_educational_attainment',
                'employee_category',
                'work_schedule',
                'provider',
                'work_hours_per_week',
                'work_hours_per_day',
                'is_teacher',
                'vacation_service_credits',
                'marital_status',
                'last_delivery_date',
                'last_leave_computation_date',
            ]);
        });
    }

};
