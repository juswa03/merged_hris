<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['regular', 'flexible', 'shift'])->default('regular');
            $table->time('work_start');
            $table->time('work_end');
            $table->unsignedSmallInteger('break_minutes')->default(60);
            $table->json('working_days'); // [1,2,3,4,5] = Mon-Fri
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_work_schedules');
    }
};
