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
        Schema::create('failed_scan_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('device_uid');
            $table->string('fingerprint_hash')->nullable(); // Hash of the scanned fingerprint for comparison
            $table->enum('failure_reason', ['template_not_found', 'quality_too_low', 'device_error', 'timeout', 'unknown'])->default('unknown');
            $table->integer('quality_score')->nullable(); // Scan quality score
            $table->string('ip_address')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['device_uid', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_scan_attempts');
    }
};
