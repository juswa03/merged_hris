<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 6. Special Skills and Hobbies
        Schema::create('special_skills_hobbies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('type'); // skill or hobby
            $table->string('description');
            $table->timestamps();
            $table->index('employee_id');
        });

        // 7. Learning and Development
        Schema::create('learning_developments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('title');
            $table->string('type'); // seminar, training, conference, workshop, etc.
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->integer('number_of_hours')->nullable();
            $table->string('location')->nullable();
            $table->string('conducted_by')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 8. Special Status
        Schema::create('special_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('status'); // PWD, Indigenous, 4Ps, etc.
            $table->text('remarks')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        // 9. Membership in Associations
        Schema::create('membership_associations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('tbl_employee')->onDelete('cascade');
            $table->string('organization_name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_associations');
        Schema::dropIfExists('special_statuses');
        Schema::dropIfExists('learning_developments');
        Schema::dropIfExists('special_skills_hobbies');
    }
};
