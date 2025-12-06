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
        Schema::create('biometric_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('tbl_employee')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('tbl_users')->onDelete('set null'); // Who performed the action
            $table->string('action'); // enrollment_created, enrollment_deleted, template_updated, device_registered, etc.
            $table->string('entity_type')->nullable(); // FingerprintTemplate, Device, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_uid')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_audit_logs');
    }
};
