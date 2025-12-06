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
            $table->integer('salary_grade')->nullable()->after('basic_salary'); // SG 1-33
            $table->integer('salary_step')->nullable()->after('salary_grade'); // Step 1-8

            // Index for salary grade lookups
            $table->index(['salary_grade', 'salary_step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_employee', function (Blueprint $table) {
            $table->dropIndex(['salary_grade', 'salary_step']);
            $table->dropColumn(['salary_grade', 'salary_step']);
        });
    }
};
