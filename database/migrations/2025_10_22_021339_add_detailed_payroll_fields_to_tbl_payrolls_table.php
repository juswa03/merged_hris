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
        Schema::table('tbl_payrolls', function (Blueprint $table) {
            // Detailed overtime breakdown
            $table->decimal('regular_overtime_hours', 8, 2)->default(0)->after('overtime_pay');
            $table->decimal('regular_overtime_pay', 10, 2)->default(0)->after('regular_overtime_hours');
            $table->decimal('restday_overtime_hours', 8, 2)->default(0)->after('regular_overtime_pay');
            $table->decimal('restday_overtime_pay', 10, 2)->default(0)->after('restday_overtime_hours');
            $table->decimal('holiday_overtime_hours', 8, 2)->default(0)->after('restday_overtime_pay');
            $table->decimal('holiday_overtime_pay', 10, 2)->default(0)->after('holiday_overtime_hours');

            // Attendance-based deductions
            $table->decimal('late_deductions', 10, 2)->default(0)->after('total_deductions');
            $table->decimal('absent_deductions', 10, 2)->default(0)->after('late_deductions');
            $table->decimal('undertime_deductions', 10, 2)->default(0)->after('absent_deductions');

            // Government deductions breakdown
            $table->decimal('sss_contribution', 10, 2)->default(0)->after('undertime_deductions');
            $table->decimal('philhealth_contribution', 10, 2)->default(0)->after('sss_contribution');
            $table->decimal('pagibig_contribution', 10, 2)->default(0)->after('philhealth_contribution');
            $table->decimal('withholding_tax', 10, 2)->default(0)->after('pagibig_contribution');

            // Other deductions (loans, advances, etc.)
            $table->decimal('other_deductions', 10, 2)->default(0)->after('withholding_tax');

            // Additional pay components
            $table->decimal('holiday_pay', 10, 2)->default(0)->after('other_deductions');
            $table->decimal('night_differential', 10, 2)->default(0)->after('holiday_pay');
            $table->decimal('bonuses', 10, 2)->default(0)->after('night_differential');

            // Gross pay for easy reference
            $table->decimal('gross_pay', 10, 2)->default(0)->after('bonuses');

            // Hourly rate (for calculation reference)
            $table->decimal('hourly_rate', 10, 2)->default(0)->after('basic_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'regular_overtime_hours',
                'regular_overtime_pay',
                'restday_overtime_hours',
                'restday_overtime_pay',
                'holiday_overtime_hours',
                'holiday_overtime_pay',
                'late_deductions',
                'absent_deductions',
                'undertime_deductions',
                'sss_contribution',
                'philhealth_contribution',
                'pagibig_contribution',
                'withholding_tax',
                'other_deductions',
                'holiday_pay',
                'night_differential',
                'bonuses',
                'gross_pay',
                'hourly_rate',
            ]);
        });
    }
};
