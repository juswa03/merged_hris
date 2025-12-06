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
        Schema::table('tbl_positions', function (Blueprint $table) {
            $table->string('title')->after('name')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('level')->nullable(); // Entry, Mid, Senior, Executive
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->integer('salary_grade')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_positions', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'requirements',
                'level',
                'min_salary',
                'max_salary',
                'salary_grade',
                'is_active'
            ]);
        });
    }
};
