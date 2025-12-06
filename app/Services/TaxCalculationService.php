<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Philippine BIR Income Tax Calculator
 * Implements 2024/2025 Progressive Tax Brackets
 * Based on BIR Tax Tables for Employees
 */
class TaxCalculationService
{
    // 2024-2025 BIR Monthly Taxable Income Tax Brackets (Employees)
    // As per BIR Tax Tables
    const TAX_BRACKETS = [
        // [min_income, max_income, base_tax, tax_rate, over_amount]
        [0, 20833, 0, 0, 0],                           // Tax-free threshold
        [20833.01, 33333, 0, 0.05, 20833],             // 5% over 20,833
        [33333.01, 66667, 625, 0.10, 33333],           // 625 + 10% over 33,333
        [66667.01, 166667, 3958.40, 0.15, 66667],      // 3,958.40 + 15% over 66,667
        [166667.01, 666667, 18958.40, 0.20, 166667],   // 18,958.40 + 20% over 166,667
        [666667.01, PHP_INT_MAX, 118958.40, 0.25, 666667], // 118,958.40 + 25% over 666,667
    ];

    // Simplified Income Exemption (SIE) - Current limit
    const SIMPLIFIED_INCOME_EXEMPTION = 250000; // Annual

    /**
     * Calculate BIR income tax based on monthly salary
     * @param float $monthlySalary - Gross monthly income
     * @param array $deductions - SSS, PhilHealth, Pag-IBIG deductions
     * @param int $overtimePay - Additional overtime pay
     * @param int $allowances - Allowances (some may be non-taxable)
     * @return array Tax calculation details
     */
    public function calculateMonthlyTax(
        $monthlySalary,
        $deductions = [],
        $overtimePay = 0,
        $allowances = 0
    ) {
        // Calculate Gross Income
        $grossIncome = $monthlySalary + $overtimePay + $allowances;

        // Calculate Taxable Income
        // Deductions: GSIS, PhilHealth, Pag-IBIG are deductible from gross income
        $gsisContribution = $deductions['gsis'] ?? ($deductions['sss'] ?? 0);
        $philhealthContribution = $deductions['philhealth'] ?? 0;
        $pagibigContribution = $deductions['pagibig'] ?? 0;

        $totalDeductibleDeductions = $gsisContribution + $philhealthContribution + $pagibigContribution;
        $taxableIncome = max(0, $grossIncome - $totalDeductibleDeductions);

        // Apply BIR Tax Brackets
        $incomeTax = $this->calculateProgressiveTax($taxableIncome);

        // Calculate effective tax rate
        $effectiveTaxRate = $taxableIncome > 0 ? ($incomeTax / $taxableIncome) * 100 : 0;

        return [
            'gross_income' => round($grossIncome, 2),
            'monthly_salary' => round($monthlySalary, 2),
            'overtime_pay' => round($overtimePay, 2),
            'allowances' => round($allowances, 2),
            'gsis_contribution' => round($gsisContribution, 2),
            'philhealth_contribution' => round($philhealthContribution, 2),
            'pagibig_contribution' => round($pagibigContribution, 2),
            'total_deductible_deductions' => round($totalDeductibleDeductions, 2),
            'taxable_income' => round($taxableIncome, 2),
            'withholding_tax' => round($incomeTax, 2),
            'effective_tax_rate' => round($effectiveTaxRate, 2),
            'breakdown' => $this->getTaxBreakdown($taxableIncome),
        ];
    }

    /**
     * Calculate progressive tax based on BIR brackets
     * @param float $taxableIncome
     * @return float Tax amount
     */
    private function calculateProgressiveTax($taxableIncome)
    {
        foreach (self::TAX_BRACKETS as $bracket) {
            [$minIncome, $maxIncome, $baseTax, $taxRate, $overAmount] = $bracket;

            if ($taxableIncome >= $minIncome && $taxableIncome <= $maxIncome) {
                if ($taxRate == 0) {
                    return 0;
                }
                
                $amountOver = $taxableIncome - $overAmount;
                $tax = $baseTax + ($amountOver * $taxRate);
                
                return max(0, $tax);
            }
        }

        return 0;
    }

    /**
     * Get detailed tax bracket breakdown
     * @param float $taxableIncome
     * @return array
     */
    private function getTaxBreakdown($taxableIncome)
    {
        $breakdown = [];

        foreach (self::TAX_BRACKETS as $bracket) {
            [$minIncome, $maxIncome, $baseTax, $taxRate, $overAmount] = $bracket;

            if ($taxableIncome >= $minIncome) {
                $bracket_income = min($taxableIncome, $maxIncome) - $minIncome;
                $bracket_tax = $bracket_income * $taxRate;

                $breakdown[] = [
                    'bracket' => "₱" . number_format($minIncome, 2) . " - ₱" . number_format($maxIncome, 2),
                    'bracket_income' => round($bracket_income, 2),
                    'rate' => ($taxRate * 100) . "%",
                    'tax_amount' => round($bracket_tax, 2),
                ];

                if ($taxableIncome < $maxIncome) {
                    break;
                }
            }
        }

        return $breakdown;
    }

    /**
     * Calculate annual tax for year-to-date reporting
     * @param float $annualGrossIncome
     * @param array $annualDeductions
     * @return array
     */
    public function calculateAnnualTax($annualGrossIncome, $annualDeductions = [])
    {
        // Convert to monthly for calculation
        $monthlyGross = $annualGrossIncome / 12;
        
        $sssAnnual = $annualDeductions['sss'] ?? 0;
        $philhealthAnnual = $annualDeductions['philhealth'] ?? 0;
        $pagibigAnnual = $annualDeductions['pagibig'] ?? 0;

        $monthly = [
            'sss' => $sssAnnual / 12,
            'philhealth' => $philhealthAnnual / 12,
            'pagibig' => $pagibigAnnual / 12,
        ];

        $monthlyTax = $this->calculateMonthlyTax($monthlyGross, $monthly);
        
        // Annualize
        $annualTax = $monthlyTax['withholding_tax'] * 12;

        return [
            'annual_gross_income' => round($annualGrossIncome, 2),
            'annual_deductible_deductions' => round($sssAnnual + $philhealthAnnual + $pagibigAnnual, 2),
            'annual_taxable_income' => round($annualGrossIncome - ($sssAnnual + $philhealthAnnual + $pagibigAnnual), 2),
            'annual_withholding_tax' => round($annualTax, 2),
            'monthly_withholding_tax' => round($monthlyTax['withholding_tax'], 2),
            'effective_annual_tax_rate' => round(($annualTax / $annualGrossIncome) * 100, 2),
        ];
    }

    /**
     * Estimate BIR Form 2316 (Certificate of Creditable Tax Withheld)
     * @param int $employeeId
     * @param int $year
     * @return array
     */
    public function generateForm2316Estimate($employeeId, $year)
    {
        // Get all payrolls for the year
        $payrolls = \App\Models\Payroll::whereHas('payrollPeriod', function($q) use ($year) {
            $q->whereYear('end_date', $year);
        })
        ->where('employee_id', $employeeId)
        ->with(['employee', 'payrollPeriod'])
        ->get();

        $totalGross = 0;
        $totalTax = 0;
        $totalGsis = 0;
        $totalPhilhealth = 0;
        $totalPagibig = 0;

        foreach ($payrolls as $payroll) {
            $totalGross += $payroll->gross_pay;
            $totalTax += $payroll->withholding_tax;
            $totalGsis += $payroll->gsis_contribution;
            $totalPhilhealth += $payroll->philhealth_contribution;
            $totalPagibig += $payroll->pagibig_contribution;
        }

        $employee = $payrolls->first()?->employee;

        return [
            'form_type' => 'BIR Form 2316',
            'tin' => $employee->tin ?? 'Not Set',
            'employee_name' => $employee->full_name ?? 'Unknown',
            'period' => $year,
            'total_compensation' => round($totalGross, 2),
            'total_tax_withheld' => round($totalTax, 2),
            'total_gsis' => round($totalGsis, 2),
            'total_philhealth' => round($totalPhilhealth, 2),
            'total_pagibig' => round($totalPagibig, 2),
            'payroll_count' => $payrolls->count(),
            'generated_date' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Check if employee qualifies for Simplified Income Exemption (SIE)
     * @param float $annualIncome
     * @return bool
     */
    public function qualifiesForSIE($annualIncome)
    {
        return $annualIncome <= self::SIMPLIFIED_INCOME_EXEMPTION;
    }

    /**
     * Compare tax liability between employee and self-employed
     * @param float $monthlyIncome
     * @return array
     */
    public function compareTaxLiability($monthlyIncome)
    {
        // Employee tax
        $employeeTax = $this->calculateMonthlyTax($monthlyIncome);

        // Self-employed tax (higher rate)
        // Self-employed individuals pay ~12% on gross income for self-employment tax
        // Plus income tax on net earnings

        $annualIncome = $monthlyIncome * 12;
        $selfEmploymentTax = $annualIncome * 0.12; // 12% self-employment tax
        $monthlySeaTax = $selfEmploymentTax / 12;

        return [
            'employee_monthly_tax' => $employeeTax['withholding_tax'],
            'employee_effective_rate' => $employeeTax['effective_tax_rate'],
            'self_employed_monthly_tax' => round($monthlySeaTax, 2),
            'self_employed_effective_rate' => 12.0,
            'savings_as_employee' => round($monthlySeaTax - $employeeTax['withholding_tax'], 2),
        ];
    }

    /**
     * Project year-to-date tax liability
     * @param float $monthlyGross
     * @param int $monthsPassed
     * @param int $totalMonthsInYear
     * @return array
     */
    public function projectYearEndTax($monthlyGross, $monthsPassed = 1, $totalMonthsInYear = 12)
    {
        $projectedAnnual = $monthlyGross * $totalMonthsInYear;
        $ytdGross = $monthlyGross * $monthsPassed;

        $monthlyTax = $this->calculateMonthlyTax($monthlyGross);
        $ytdTax = $monthlyTax['withholding_tax'] * $monthsPassed;
        $projectedAnnualTax = $monthlyTax['withholding_tax'] * $totalMonthsInYear;

        return [
            'ytd_gross' => round($ytdGross, 2),
            'projected_annual_gross' => round($projectedAnnual, 2),
            'ytd_tax' => round($ytdTax, 2),
            'projected_annual_tax' => round($projectedAnnualTax, 2),
            'months_passed' => $monthsPassed,
            'projected_effective_rate' => round(($projectedAnnualTax / $projectedAnnual) * 100, 2),
        ];
    }

    /**
     * Generate tax calculation report for payroll period
     * @param \App\Models\PayrollPeriod $period
     * @return array
     */
    public function generateTaxReport(\App\Models\PayrollPeriod $period)
    {
        $payrolls = \App\Models\Payroll::where('payroll_period_id', $period->id)
            ->with(['employee', 'payrollPeriod'])
            ->get();

        $report = [
            'period' => $period->period_name,
            'payroll_count' => $payrolls->count(),
            'total_gross_income' => 0,
            'total_tax_withheld' => 0,
            'total_deductions' => 0,
            'average_tax_rate' => 0,
            'highest_earner' => null,
            'lowest_earner' => null,
            'employees' => [],
        ];

        $highestTax = 0;
        $lowestTax = PHP_INT_MAX;

        foreach ($payrolls as $payroll) {
            $report['total_gross_income'] += $payroll->gross_pay;
            $report['total_tax_withheld'] += $payroll->withholding_tax;
            $report['total_deductions'] += $payroll->total_deductions;

            $taxRate = $payroll->gross_pay > 0 ? ($payroll->withholding_tax / $payroll->gross_pay) * 100 : 0;

            $report['employees'][] = [
                'employee_id' => $payroll->employee_id,
                'employee_name' => $payroll->employee->full_name,
                'gross_income' => round($payroll->gross_pay, 2),
                'tax_withheld' => round($payroll->withholding_tax, 2),
                'effective_rate' => round($taxRate, 2),
                'net_income' => round($payroll->net_pay, 2),
            ];

            if ($payroll->withholding_tax > $highestTax) {
                $highestTax = $payroll->withholding_tax;
                $report['highest_earner'] = [
                    'name' => $payroll->employee->full_name,
                    'tax' => round($highestTax, 2),
                ];
            }

            if ($payroll->withholding_tax < $lowestTax) {
                $lowestTax = $payroll->withholding_tax;
                $report['lowest_earner'] = [
                    'name' => $payroll->employee->full_name,
                    'tax' => round($lowestTax, 2),
                ];
            }
        }

        if ($report['total_gross_income'] > 0) {
            $report['average_tax_rate'] = round(($report['total_tax_withheld'] / $report['total_gross_income']) * 100, 2);
        }

        $report['total_gross_income'] = round($report['total_gross_income'], 2);
        $report['total_tax_withheld'] = round($report['total_tax_withheld'], 2);
        $report['total_deductions'] = round($report['total_deductions'], 2);

        return $report;
    }

    /**
     * Get BIR tax brackets for display
     * @return array
     */
    public static function getTaxBrackets()
    {
        return [
            [
                'min' => 0,
                'max' => 20833,
                'rate' => '0%',
                'description' => 'Tax-free threshold'
            ],
            [
                'min' => 20833.01,
                'max' => 33333,
                'rate' => '5%',
                'description' => '5% on income over ₱20,833'
            ],
            [
                'min' => 33333.01,
                'max' => 66667,
                'rate' => '10%',
                'description' => '₱625 + 10% on income over ₱33,333'
            ],
            [
                'min' => 66667.01,
                'max' => 166667,
                'rate' => '15%',
                'description' => '₱3,958.40 + 15% on income over ₱66,667'
            ],
            [
                'min' => 166667.01,
                'max' => 666667,
                'rate' => '20%',
                'description' => '₱18,958.40 + 20% on income over ₱166,667'
            ],
            [
                'min' => 666667.01,
                'max' => 999999999,
                'rate' => '25%',
                'description' => '₱118,958.40 + 25% on income over ₱666,667'
            ],
        ];
    }
}
