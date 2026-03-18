<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 11. Financial Assets
        Schema::create('assets_real_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('property_type'); // Land, House, Apartment, Commercial, etc.
            $table->text('description');
            $table->text('location');
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->string('acquisition_mode')->nullable(); // Purchased, Inherited, etc.
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('assets_personal_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('property_type'); // Vehicle, Jewelry, Equipment, etc.
            $table->text('description');
            $table->decimal('estimated_value', 15, 2)->nullable();
            $table->string('acquisition_mode')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        Schema::create('total_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('cost_category');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->date('date_incurred')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('total_costs');
        Schema::dropIfExists('assets_personal_properties');
        Schema::dropIfExists('assets_real_properties');
    }
};
