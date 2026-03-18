<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 19. Task Management
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('tbl_employee')->onDelete('set null');
            $table->foreignId('assigned_by')->constrained('tbl_users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->index('status');
            $table->index('due_date');
        });

        // 20. Activity Log
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('tbl_employee')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('tbl_users')->onDelete('set null');
            $table->string('action');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->index('employee_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        // 21. Event Management
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable();
            $table->string('type'); // Company Event, Holiday, Training, etc.
            $table->string('status')->default('scheduled'); // scheduled, ongoing, completed, cancelled
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index('start_date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('tasks');
    }
};
