<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_maintenance_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(false);
            $table->string('title', 200)->default('System Maintenance');
            $table->text('message')->default('We are currently performing scheduled maintenance. Please check back soon.');
            $table->text('whitelisted_ips')->nullable(); // comma-separated
            $table->timestamp('scheduled_end_at')->nullable();
            $table->foreignId('activated_by')->nullable()->constrained('tbl_users')->nullOnDelete();
            $table->timestamps();
        });

        // Seed single row
        DB::table('tbl_maintenance_settings')->insert([
            'is_active'    => false,
            'title'        => 'System Maintenance',
            'message'      => 'We are currently performing scheduled maintenance. Please check back soon.',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_maintenance_settings');
    }
};
