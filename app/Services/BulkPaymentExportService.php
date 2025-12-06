<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

class BulkPaymentExportService
{
    /**
     * Generate ACH (Automated Clearing House) format file
     * Standard for Philippine bank transfers
     */
    public function generateACHFormat(PayrollPeriod $period, $bankCode = '001')
    {
        $payrolls = Payroll::where('payroll_period_id', $period->id)
            ->where('status', 'processed')
            ->with('employee')
            ->get();

        $achFile = $this->generateACHHeader($period, $payrolls);
        
        foreach ($payrolls as $payroll) {
            $achFile .= $this->generateACHDetail($payroll);
        }

        $achFile .= $this->generateACHTrailer($period, $payrolls);

        return $achFile;
    }

    /**
     * Generate CSV format for Excel/spreadsheet import
     */
    public function generateCSVFormat(PayrollPeriod $period)
    {
        $payrolls = Payroll::where('payroll_period_id', $period->id)
            ->where('status', 'processed')
            ->with(['employee', 'payrollPeriod'])
            ->get();

        $csv = "Employee ID,Employee Name,Account Number,Bank Name,Net Pay,Reference,Date\n";

        foreach ($payrolls as $payroll) {
            $employeeId = $payroll->employee->employee_id ?? '';
            $name = trim($payroll->employee->first_name . ' ' . $payroll->employee->last_name);
            $accountNumber = $payroll->employee->bank_account_number ?? '';
            $bankName = $payroll->employee->bank_name ?? 'Standard Chartered Bank';
            $netPay = number_format($payroll->net_pay, 2);
            $reference = "PR-{$period->id}-{$payroll->employee_id}";
            $date = $period->pay_date->format('Y-m-d');

            $csv .= "\"{$employeeId}\",\"{$name}\",\"{$accountNumber}\",\"{$bankName}\",{$netPay},\"{$reference}\",\"{$date}\"\n";
        }

        return $csv;
    }

    /**
     * Generate Bank-specific format (BPI, BDO, PNB, etc.)
     */
    public function generateBankSpecificFormat(PayrollPeriod $period, $bankCode = 'BPI')
    {
        $payrolls = Payroll::where('payroll_period_id', $period->id)
            ->where('status', 'processed')
            ->with(['employee', 'payrollPeriod'])
            ->get();

        return match($bankCode) {
            'BPI' => $this->generateBPIFormat($period, $payrolls),
            'BDO' => $this->generateBDOFormat($period, $payrolls),
            'PNB' => $this->generatePNBFormat($period, $payrolls),
            'METROBANK' => $this->generateMetrobankFormat($period, $payrolls),
            default => $this->generateCSVFormat($period),
        };
    }

    /**
     * BPI Format
     */
    private function generateBPIFormat(PayrollPeriod $period, Collection $payrolls)
    {
        $output = "";
        $output .= "101";  // Record type
        $output .= str_pad(now()->format('YmdHi'), 10);  // Timestamp
        $output .= str_pad($payrolls->count(), 6, '0', STR_PAD_LEFT);  // Record count
        $output .= str_pad(number_format($payrolls->sum('net_pay'), 2, '', ''), 15, '0', STR_PAD_LEFT);  // Total amount
        $output .= "\n";

        foreach ($payrolls as $payroll) {
            $output .= "201";  // Detail record type
            $output .= str_pad($payroll->employee->bank_account_number ?? '', 20);  // Account
            $output .= str_pad(number_format($payroll->net_pay, 2, '', ''), 15, '0', STR_PAD_LEFT);  // Amount
            $output .= str_pad($payroll->employee->first_name . ' ' . $payroll->employee->last_name, 30);  // Name
            $output .= "\n";
        }

        $output .= "900";  // Trailer record
        $output .= str_pad($payrolls->count(), 6, '0', STR_PAD_LEFT);
        $output .= str_pad(number_format($payrolls->sum('net_pay'), 2, '', ''), 15, '0', STR_PAD_LEFT);

        return $output;
    }

    /**
     * BDO Format
     */
    private function generateBDOFormat(PayrollPeriod $period, Collection $payrolls)
    {
        $csv = "Reference Number,Beneficiary Name,Beneficiary Account,Amount\n";

        foreach ($payrolls as $payroll) {
            $reference = "PR" . str_pad($payroll->id, 10, '0', STR_PAD_LEFT);
            $name = trim($payroll->employee->first_name . ' ' . $payroll->employee->last_name);
            $account = $payroll->employee->bank_account_number ?? '';
            $amount = number_format($payroll->net_pay, 2);

            $csv .= "\"{$reference}\",\"{$name}\",\"{$account}\",{$amount}\n";
        }

        return $csv;
    }

    /**
     * PNB Format
     */
    private function generatePNBFormat(PayrollPeriod $period, Collection $payrolls)
    {
        $csv = "Receiving Bank,Account Type,Account Number,Account Name,Amount,Reference\n";

        foreach ($payrolls as $payroll) {
            $bank = $payroll->employee->bank_name ?? '';
            $accountType = 'SAVINGS';
            $account = $payroll->employee->bank_account_number ?? '';
            $name = trim($payroll->employee->first_name . ' ' . $payroll->employee->last_name);
            $amount = number_format($payroll->net_pay, 2);
            $reference = "PR-{$period->id}-{$payroll->employee_id}";

            $csv .= "\"{$bank}\",\"{$accountType}\",\"{$account}\",\"{$name}\",{$amount},\"{$reference}\"\n";
        }

        return $csv;
    }

    /**
     * Metrobank Format
     */
    private function generateMetrobankFormat(PayrollPeriod $period, Collection $payrolls)
    {
        $csv = "Batch ID,Account Number,Account Name,Amount,Debit Account\n";

        $batchId = $period->id . now()->format('Ymd');

        foreach ($payrolls as $payroll) {
            $account = $payroll->employee->bank_account_number ?? '';
            $name = trim($payroll->employee->first_name . ' ' . $payroll->employee->last_name);
            $amount = number_format($payroll->net_pay, 2);
            $debitAccount = '1234567890';  // Company's account

            $csv .= "\"{$batchId}\",\"{$account}\",\"{$name}\",{$amount},\"{$debitAccount}\"\n";
        }

        return $csv;
    }

    /**
     * Generate ACH Header
     */
    private function generateACHHeader(PayrollPeriod $period, Collection $payrolls)
    {
        $header = "101 ";
        $header .= str_pad("121000248", 9) . " ";  // Sending bank
        $header .= str_pad("123456789", 9) . " ";  // Receiving bank
        $header .= now()->format("ymdHi") . " ";
        $header .= str_pad("", 6) . " ";
        $header .= str_pad("094101", 6) . " ";
        $header .= str_pad(strlen("PAYROLL"), 3, '0', STR_PAD_LEFT) . " ";
        $header .= str_pad("Payroll Batch", 13) . "\n";

        return $header;
    }

    /**
     * Generate ACH Detail Record
     */
    private function generateACHDetail(Payroll $payroll)
    {
        $detail = "621 ";
        $detail .= str_pad("031000456", 9) . " ";  // Bank routing
        $detail .= str_pad($payroll->employee->bank_account_number ?? '', 17) . " ";
        $detail .= str_pad(str_replace([',', '.'], '', number_format($payroll->net_pay, 2)), 10, '0', STR_PAD_LEFT) . " ";
        $detail .= now()->format("ymdHi") . " ";
        $detail .= str_pad($payroll->employee->first_name . " " . $payroll->employee->last_name, 16) . "\n";

        return $detail;
    }

    /**
     * Generate ACH Trailer
     */
    private function generateACHTrailer(PayrollPeriod $period, Collection $payrolls)
    {
        $trailer = "820 ";
        $trailer .= str_pad($payrolls->count(), 6, '0', STR_PAD_LEFT) . " ";
        $trailer .= str_pad($payrolls->count(), 6, '0', STR_PAD_LEFT) . " ";
        $trailer .= str_pad(str_replace([',', '.'], '', number_format($payrolls->sum('net_pay'), 2)), 12, '0', STR_PAD_LEFT) . " ";
        $trailer .= str_pad("000000000000", 12) . " ";
        $trailer .= str_pad("", 39) . "\n";

        return $trailer;
    }

    /**
     * Summary report
     */
    public function generateSummaryReport(PayrollPeriod $period)
    {
        $payrolls = Payroll::where('payroll_period_id', $period->id)
            ->where('status', 'processed')
            ->with(['employee', 'payrollPeriod'])
            ->get();

        return [
            'period' => $period->period_name,
            'pay_date' => $period->pay_date->format('Y-m-d'),
            'total_employees' => $payrolls->count(),
            'total_amount' => $payrolls->sum('net_pay'),
            'average_pay' => $payrolls->count() > 0 ? $payrolls->sum('net_pay') / $payrolls->count() : 0,
            'total_gross' => $payrolls->sum('gross_pay'),
            'total_deductions' => $payrolls->sum('total_deductions'),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
