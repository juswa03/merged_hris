<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeductionType;
use App\Models\Deduction;
use App\Models\Allowance;
class DeductionsAndAllowancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Create deduction types
        $deductionTypes = [
            ['name' => 'Government Mandated'],
            ['name' => 'Loans'],
            ['name' => 'Advances'],
            ['name' => 'Other Deductions'],
        ];

        foreach ($deductionTypes as $type) {
            DeductionType::create($type);
        }

        // Create government deductions
        $governmentType = DeductionType::where('name', 'Government Mandated')->first();
        
        $deductions = [
            ['name' => 'SSS', 'amount' => 1125.00, 'deduction_type_id' => $governmentType->id],
            ['name' => 'PhilHealth', 'amount' => 400.00, 'deduction_type_id' => $governmentType->id],
            ['name' => 'Pag-IBIG', 'amount' => 100.00, 'deduction_type_id' => $governmentType->id],
            ['name' => 'Withholding Tax', 'amount' => 1875.00, 'deduction_type_id' => $governmentType->id],
        ];

        foreach ($deductions as $deduction) {
            Deduction::create($deduction);
        }

        // Create allowances
        $allowances = [
            ['name' => 'Transportation Allowance', 'amount' => 2000.00, 'type' => 'monthly'],
            ['name' => 'Meal Allowance', 'amount' => 1500.00, 'type' => 'monthly'],
            ['name' => 'Clothing Allowance', 'amount' => 5000.00, 'type' => 'annual'],
            ['name' => 'Communication Allowance', 'amount' => 1000.00, 'type' => 'monthly'],
        ];

        foreach ($allowances as $allowance) {
            Allowance::create($allowance);
        }

    }
}
