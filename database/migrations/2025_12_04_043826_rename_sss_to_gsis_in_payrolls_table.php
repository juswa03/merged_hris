<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_payrolls', function (Blueprint $table) {
            // $table->renameColumn('sss_contribution', 'gsis_contribution');
            DB::statement("ALTER TABLE tbl_payrolls CHANGE sss_contribution gsis_contribution DECIMAL(10,2) NULL DEFAULT 0");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_payrolls', function (Blueprint $table) {
            // $table->renameColumn('gsis_contribution', 'sss_contribution');
            DB::statement("ALTER TABLE tbl_payrolls CHANGE gsis_contribution sss_contribution DECIMAL(10,2) NULL DEFAULT 0");
        });
    }
};
