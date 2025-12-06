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
        Schema::table('tbl_attendance', function (Blueprint $table) {
            // Add foreign key constraint
            $table->foreign('device_uid')
                  ->references('device_uid')
                  ->on('devices')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_attendance', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['device_uid']);
        });
    }
};
