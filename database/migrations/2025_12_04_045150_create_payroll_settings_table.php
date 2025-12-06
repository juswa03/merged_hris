<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('value');
            $table->string('type')->default('text'); // text, number, boolean, json
            $table->string('group')->index(); // gsis, philhealth, pagibig, tax
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default values
        DB::table('payroll_settings')->insert([
            // GSIS Settings
            [
                'key' => 'gsis_rate',
                'name' => 'GSIS Contribution Rate',
                'value' => '0.09',
                'type' => 'number',
                'group' => 'gsis',
                'description' => 'Employee share percentage (e.g., 0.09 for 9%)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // PhilHealth Settings
            [
                'key' => 'philhealth_rate',
                'name' => 'PhilHealth Contribution Rate',
                'value' => '0.05',
                'type' => 'number',
                'group' => 'philhealth',
                'description' => 'Total contribution rate (e.g., 0.05 for 5%)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'philhealth_min_contribution',
                'name' => 'PhilHealth Minimum Contribution',
                'value' => '500.00',
                'type' => 'number',
                'group' => 'philhealth',
                'description' => 'Minimum monthly contribution amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'philhealth_max_contribution',
                'name' => 'PhilHealth Maximum Contribution',
                'value' => '5000.00',
                'type' => 'number',
                'group' => 'philhealth',
                'description' => 'Maximum monthly contribution amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pag-IBIG Settings
            [
                'key' => 'pagibig_rate',
                'name' => 'Pag-IBIG Contribution Rate',
                'value' => '0.02',
                'type' => 'number',
                'group' => 'pagibig',
                'description' => 'Employee share percentage (e.g., 0.02 for 2%)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pagibig_min_contribution',
                'name' => 'Pag-IBIG Minimum Contribution',
                'value' => '100.00',
                'type' => 'number',
                'group' => 'pagibig',
                'description' => 'Minimum monthly contribution amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pagibig_max_contribution',
                'name' => 'Pag-IBIG Maximum Contribution',
                'value' => '200.00',
                'type' => 'number',
                'group' => 'pagibig',
                'description' => 'Maximum monthly contribution amount',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
