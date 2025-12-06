<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Models\DtrEntry;
use App\Models\PayrollPeriod;
use App\Models\Holiday;
use App\Services\DtrToPayrollService;
use App\Services\PayrollCalculationService;
use Carbon\Carbon;
use Tests\TestCase;

class DtrToPayrollIntegrationTest extends TestCase
{
    protected $dtrToPayrollService;
    protected $payrollService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->payrollService = new PayrollCalculationService();
        $this->dtrToPayrollService = new DtrToPayrollService($this->payrollService);
    }

    /**
     * Test DTR validation identifies missing entries
     */
    public function test_dtr_validation_identifies_missing_entries()
    {
        $employee = Employee::factory()->create(['basic_salary' => 16000]);
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // No DTR entries created - validation should fail
        $validation = $this->dtrToPayrollService->validateDtrForPayroll($period);

        $this->assertGreaterThan(0, $validation['missing_count']);
        $this->assertContains($employee->id, array_column($validation['missing_employees'], 'employee_id'));
    }

    /**
     * Test DTR validation identifies incomplete entries
     */
    public function test_dtr_validation_identifies_incomplete_entries()
    {
        $employee = Employee::factory()->create(['basic_salary' => 16000]);
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Create only 10 DTR entries (assuming 22 work days)
        for ($i = 0; $i < 10; $i++) {
            DtrEntry::create([
                'employee_id' => $employee->id,
                'dtr_date' => now()->startOfMonth()->addDays($i),
                'total_hours' => 8,
                'total_minutes' => 0,
                'under_time_minutes' => 0,
                'status' => 'present',
            ]);
        }

        $validation = $this->dtrToPayrollService->validateDtrForPayroll($period);

        $this->assertGreaterThan(0, $validation['incomplete_count']);
    }

    /**
     * Test DTR data quality check catches invalid times
     */
    public function test_dtr_quality_check_catches_invalid_times()
    {
        $employee = Employee::factory()->create();
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Create DTR with invalid time sequence (departure before arrival)
        DtrEntry::create([
            'employee_id' => $employee->id,
            'dtr_date' => now(),
            'am_arrival' => '10:00',
            'am_departure' => '08:00', // Invalid: before arrival
            'total_hours' => 0,
            'total_minutes' => 0,
            'under_time_minutes' => 0,
            'status' => 'present',
        ]);

        $validation = $this->dtrToPayrollService->validateDtrForPayroll($period);

        $this->assertGreaterThan(0, count($validation['issues']));
        $this->assertStringContainsString('Invalid', $validation['issues'][0]['issue']);
    }

    /**
     * Test DTR summary calculation
     */
    public function test_dtr_summary_calculation()
    {
        $employee = Employee::factory()->create(['basic_salary' => 16000]);
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Create DTR entries
        DtrEntry::create([
            'employee_id' => $employee->id,
            'dtr_date' => now()->startOfMonth(),
            'am_arrival' => '08:00',
            'am_departure' => '12:00',
            'pm_arrival' => '13:00',
            'pm_departure' => '17:00',
            'total_hours' => 8,
            'total_minutes' => 0,
            'under_time_minutes' => 0,
            'status' => 'present',
        ]);

        $summary = $this->dtrToPayrollService->getDtrSummaryForPayroll($period);

        $this->assertNotEmpty($summary['employees_summary']);
        $this->assertEquals(1, $summary['totals']['total_employees']);
        $this->assertGreaterThan(0, $summary['totals']['total_hours_worked']);
    }

    /**
     * Test payroll generation from DTR
     */
    public function test_payroll_generation_from_dtr()
    {
        $employee = Employee::factory()->create(['basic_salary' => 16000]);
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Create complete DTR entries for all working days
        $workDays = 0;
        $current = $period->start_date->copy();
        
        while ($current <= $period->end_date) {
            if (!$current->isWeekend()) {
                DtrEntry::create([
                    'employee_id' => $employee->id,
                    'dtr_date' => $current,
                    'am_arrival' => '08:00',
                    'am_departure' => '12:00',
                    'pm_arrival' => '13:00',
                    'pm_departure' => '17:00',
                    'total_hours' => 8,
                    'total_minutes' => 0,
                    'under_time_minutes' => 0,
                    'status' => 'present',
                ]);
                $workDays++;
            }
            $current->addDay();
        }

        $result = $this->dtrToPayrollService->generatePayrollFromDtr($period);

        $this->assertGreaterThan(0, $result['generated_count']);
        $this->assertGreaterThan(0, $result['total_gross_pay']);
    }

    /**
     * Test payroll recalculation after DTR change
     */
    public function test_payroll_recalculation_after_dtr_change()
    {
        $employee = Employee::factory()->create(['basic_salary' => 16000]);
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Create initial DTR
        $dtrEntry = DtrEntry::create([
            'employee_id' => $employee->id,
            'dtr_date' => now(),
            'am_arrival' => '08:00',
            'am_departure' => '12:00',
            'pm_arrival' => '13:00',
            'pm_departure' => '17:00',
            'total_hours' => 8,
            'total_minutes' => 0,
            'under_time_minutes' => 0,
            'status' => 'present',
        ]);

        // Generate initial payroll
        $initialResult = $this->dtrToPayrollService->recalculatePayrollFromDtr($employee, $period);
        $initialNetPay = $initialResult['net_pay'];

        // Modify DTR (add undertime)
        $dtrEntry->update([
            'am_departure' => '11:00', // 1 hour less
            'total_hours' => 7,
            'under_time_minutes' => 60,
            'status' => 'undertime',
        ]);

        // Recalculate payroll
        $recalculatedResult = $this->dtrToPayrollService->recalculatePayrollFromDtr($employee, $period);
        $recalculatedNetPay = $recalculatedResult['net_pay'];

        // Net pay should be lower due to undertime deduction
        $this->assertLessThan($initialNetPay, $recalculatedNetPay);
    }

    /**
     * Test DTR to payroll with holidays
     */
    public function test_dtr_to_payroll_with_holiday_premium()
    {
        $employee = Employee::factory()->create(['basic_salary' => 16000]);
        
        $holidayDate = now()->format('Y-m-d');
        Holiday::create([
            'date' => $holidayDate,
            'name' => 'Test Holiday',
            'type' => 'regular',
            'is_paid' => true,
        ]);

        $period = PayrollPeriod::factory()->create([
            'start_date' => Carbon::parse($holidayDate)->startOfMonth(),
            'end_date' => Carbon::parse($holidayDate)->endOfMonth(),
        ]);

        // Create DTR for holiday
        DtrEntry::create([
            'employee_id' => $employee->id,
            'dtr_date' => $holidayDate,
            'am_arrival' => '08:00',
            'am_departure' => '12:00',
            'pm_arrival' => '13:00',
            'pm_departure' => '17:00',
            'total_hours' => 8,
            'total_minutes' => 0,
            'under_time_minutes' => 0,
            'is_holiday' => true,
            'status' => 'present',
        ]);

        $summary = $this->dtrToPayrollService->getDtrSummaryForPayroll($period);

        $this->assertGreaterThan(0, $summary['employees_summary'][0]['holiday_days']);
    }

    /**
     * Test DTR validation warns about high hours
     */
    public function test_dtr_validation_warns_about_excessive_hours()
    {
        $employee = Employee::factory()->create();
        
        $period = PayrollPeriod::factory()->create([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);

        // Create DTR with excessive hours (18 hours)
        DtrEntry::create([
            'employee_id' => $employee->id,
            'dtr_date' => now(),
            'total_hours' => 18,
            'total_minutes' => 0,
            'under_time_minutes' => 0,
            'status' => 'present',
        ]);

        $validation = $this->dtrToPayrollService->validateDtrForPayroll($period);

        $this->assertGreaterThan(0, count($validation['issues']));
        $this->assertStringContainsString('high', strtolower($validation['issues'][0]['issue']));
    }
}
