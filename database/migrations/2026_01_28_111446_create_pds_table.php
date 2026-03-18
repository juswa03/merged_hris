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
        Schema::create('pds', function (Blueprint $table) {
            $table->id();
            
            // Employee reference
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            
            // Status and workflow
            $table->string('status')->default('incomplete'); // incomplete, submitted, under_review, verified, rejected
            
            // Timestamps for workflow tracking
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('last_action_at')->nullable();
            
            // Action tracking
            $table->foreignId('last_action_by')->nullable()->constrained('tbl_users');
            $table->text('verification_remarks')->nullable();
            
            // PDS data as JSON
            $table->json('data')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('submitted_at');
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pds');
    }
};
