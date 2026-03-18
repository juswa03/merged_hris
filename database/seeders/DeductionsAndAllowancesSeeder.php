<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeductionType;
use App\Models\Deduction;
use App\Models\Allowance;
use App\Models\PayrollPeriod;
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

        // Get or create a default payroll period for time_stamp_id
        $payrollPeriod = PayrollPeriod::first();
        $timeStampId = $payrollPeriod ? $payrollPeriod->id : 1;

        // Create allowances
        $allowances = [
            ['name' => 'Transportation Allowance', 'amount' => 2000.00, 'type' => 'monthly', 'time_stamp_id' => $timeStampId],
            ['name' => 'Meal Allowance', 'amount' => 1500.00, 'type' => 'monthly', 'time_stamp_id' => $timeStampId],
            ['name' => 'Clothing Allowance', 'amount' => 5000.00, 'type' => 'annual', 'time_stamp_id' => $timeStampId],
            ['name' => 'Communication Allowance', 'amount' => 1000.00, 'type' => 'monthly', 'time_stamp_id' => $timeStampId],
        ];

        foreach ($allowances as $allowance) {
            Allowance::create($allowance);
        }

    }
}
