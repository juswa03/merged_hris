<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_employee', function (Blueprint $table) {
            $table->foreignId('work_schedule_id')
                  ->nullable()
                  ->after('job_status_id')
                  ->constrained('tbl_work_schedules')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_employee', function (Blueprint $table) {
            $table->dropForeign(['work_schedule_id']);
            $table->dropColumn('work_schedule_id');
        });
    }
};
