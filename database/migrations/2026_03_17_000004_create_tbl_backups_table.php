<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('type', 20)->default('full'); // full, database, storage
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('path');
            $table->string('status', 20)->default('pending'); // pending, completed, failed
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('tbl_users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_backups');
    }
};
