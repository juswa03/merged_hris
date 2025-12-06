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
            $table->decimal('basic_salary', 10, 2)->default(0)->after('job_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_employee', function (Blueprint $table) {
            $table->dropColumn('basic_salary');
        });
    }
};
