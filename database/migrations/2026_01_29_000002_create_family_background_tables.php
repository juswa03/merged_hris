<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2. Family Background (Parent Info, Spouse, Children)
        Schema::create('parent_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('parent_type'); // father, mother
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('spouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->date('date_of_birth');
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
        Schema::dropIfExists('spouses');
        Schema::dropIfExists('parent_infos');
    }
};
