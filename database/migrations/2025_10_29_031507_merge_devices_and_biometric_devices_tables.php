<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Note: This migration is deprecated. The merging of biometric_devices 
     * and devices tables was already completed by migration
     * 2025_10_29_033750_migrate_biometric_devices_data_manually.
     * This is now a no-op migration to maintain migration history.
     */
    public function up(): void
    {
        // No-op: Tables have already been merged by a later migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: No changes to rollback
    }
};

