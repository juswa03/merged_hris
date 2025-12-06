<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee->last_name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .payslip-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .payslip-header {
            background-color: #1e40af; /* Fallback for gradient */
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo {
            height: 50px;
            width: auto;
            margin-right: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .company-sub {
            font-size: 12px;
            color: #bfdbfe; /* blue-100 */
            margin: 0;
        }
        .payslip-title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }
        .payslip-subtitle {
            font-size: 12px;
            color: #bfdbfe;
            margin: 0;
        }
        .pay-date-label {
            font-size: 14px;
            font-weight: 600;
        }
        .pay-date-value {
            font-size: 18px;
            font-weight: bold;
        }
        .payslip-id {
            font-size: 12px;
            color: #bfdbfe;
            margin-top: 5px;
        }
        
        /* Body Sections */
        .section-container {
            padding: 0 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f9fafb; /* gray-50 */
        }
        .info-table td {
            padding: 10px;
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            font-weight: 600;
            color: #374151; /* gray-700 */
            margin-bottom: 5px;
            display: block;
        }
        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827; /* gray-900 */
            margin: 0;
        }
        .info-sub {
            font-size: 12px;
            color: #4b5563; /* gray-600 */
            margin: 2px 0 0 0;
        }

        /* Earnings & Deductions */
        .columns-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .columns-table td {
            vertical-align: top;
            padding: 0 10px;
        }
        .columns-table td:first-child {
            padding-left: 0;
        }
        .columns-table td:last-child {
            padding-right: 0;
        }
        
        .section-title {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        .breakdown-table td {
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .breakdown-table tr:last-child td {
            border-bottom: none;
        }
        .item-name {
            color: #4b5563;
        }
        .amount-cell {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        .total-row td {
            background-color: #f8fafc;
            border-top: 2px solid #e2e8f0;
            font-weight: 700;
            padding: 8px 0;
        }

        /* Net Pay Summary */
        .summary-box {
            background-color: #eff6ff; /* blue-50 */
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-table {
            width: 100%;
            text-align: center;
        }
        .summary-label {
            font-size: 12px;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }
        .text-red { color: #dc2626; }
        .text-green { color: #16a34a; }

        /* Signature */
        .signature-area {
            margin-top: 40px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 20px;
        }
        .signature-table {
            width: 100%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #9ca3af;
            width: 80%;
            margin: 30px auto 5px auto;
        }
        .signatory-name {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }
        .signatory-title {
            font-size: 10px;
            color: #4b5563;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <!-- Header -->
        <div class="payslip-header">
            <table class="header-table">
                <tr>
                    <td>
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 60px;">
                                    <!-- Use absolute path or base64 for PDF images if needed, but asset() usually works with dompdf if configured -->
                                    <img src="{{ public_path('images/logos/uni_logo.png') }}" alt="Logo" class="logo">
                                </td>
                                <td>
                                    <p class="company-name">BIPSU HUMAN RESOURCE</p>
                                    <p class="company-sub">Biliran Province State University</p>
                                </td>
                            </tr>
                        </table>
                        <p class="payslip-title">PAYSLIP</p>
                        <p class="payslip-subtitle">Salary Statement</p>
                    </td>
                    <td style="text-align: right;">
                        <p class="pay-date-label">Pay Date</p>
                        <p class="pay-date-value">{{ \Carbon\Carbon::parse($payroll->created_at)->format('M d, Y') }}</p>
                        <p class="payslip-id">Payslip #{{ $payroll->id }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-container">
            <!-- Employee Information -->
            <table class="info-table">
                <tr>
                    <td>
                        <span class="info-label">Employee Information</span>
                        <p class="info-value">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                        <p class="info-sub">{{ $employee->position->name ?? 'N/A' }}</p>
                        <p class="info-sub">{{ $employee->department->name ?? 'N/A' }}</p>
                        <p class="info-sub">Employee ID: {{ $employee->employee_id }}</p>
                    </td>
                    <td>
                        <span class="info-label">Pay Period</span>
                        <p class="info-value">
                            @if($payroll->payrollPeriod && $payroll->payrollPeriod->start_date && $payroll->payrollPeriod->end_date)
                                {{ \Carbon\Carbon::parse($payroll->payrollPeriod->start_date)->format('M d, Y') }} - 
                                {{ \Carbon\Carbon::parse($payroll->payrollPeriod->end_date)->format('M d, Y') }}
                            @else
                                Current Period
                            @endif
                        </p>
                        
                        <div style="margin-top: 15px;">
                            <span class="info-label">Payment Method</span>
                            <p class="info-value">Bank Transfer</p>
                            <p class="info-sub">Account: ****{{ substr($employee->bank_account_number ?? '0000', -4) }}</p>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Earnings & Deductions -->
            <table class="columns-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="section-title">EARNINGS</div>
                        <table class="breakdown-table">
                            <tr>
                                <td class="item-name">Basic Salary</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->basic_salary, 2) }}</td>
                            </tr>
                            
                            @if($payroll->overtime_pay > 0)
                            <tr>
                                <td class="item-name">Overtime Pay</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->overtime_pay, 2) }}</td>
                            </tr>
                            @endif
                            
                            @if(($payroll->holiday_pay ?? 0) > 0)
                            <tr>
                                <td class="item-name">Holiday Pay</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->holiday_pay, 2) }}</td>
                            </tr>
                            @endif
                            
                            @if(($payroll->night_differential ?? 0) > 0)
                            <tr>
                                <td class="item-name">Night Differential</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->night_differential, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->bonuses ?? 0) > 0)
                            <tr>
                                <td class="item-name">Bonuses & Incentives</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->bonuses, 2) }}</td>
                            </tr>
                            @endif

                            <tr class="total-row">
                                <td>Total Earnings</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->gross_salary, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%;">
                        <div class="section-title">DEDUCTIONS</div>
                        <table class="breakdown-table">
                            @if(($payroll->gsis_contribution ?? 0) > 0)
                            <tr>
                                <td class="item-name">GSIS Contribution</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->gsis_contribution, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->philhealth_contribution ?? 0) > 0)
                            <tr>
                                <td class="item-name">PhilHealth</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->philhealth_contribution, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->pagibig_contribution ?? 0) > 0)
                            <tr>
                                <td class="item-name">Pag-IBIG Fund</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->pagibig_contribution, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->withholding_tax ?? 0) > 0)
                            <tr>
                                <td class="item-name">Withholding Tax</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->withholding_tax, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->late_deductions ?? 0) > 0)
                            <tr>
                                <td class="item-name">Late/Tardiness</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->late_deductions, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->absent_deductions ?? 0) > 0)
                            <tr>
                                <td class="item-name">Absences</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->absent_deductions, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->undertime_deductions ?? 0) > 0)
                            <tr>
                                <td class="item-name">Undertime</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->undertime_deductions, 2) }}</td>
                            </tr>
                            @endif

                            @if(($payroll->other_deductions ?? 0) > 0)
                            <tr>
                                <td class="item-name">Other Deductions</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->other_deductions, 2) }}</td>
                            </tr>
                            @endif

                            <tr class="total-row">
                                <td>Total Deductions</td>
                                <td class="amount-cell">₱ {{ number_format($payroll->total_deductions, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Net Pay Summary -->
            <div class="summary-box">
                <table class="summary-table">
                    <tr>
                        <td>
                            <div class="summary-label">Gross Salary</div>
                            <div class="summary-value">₱ {{ number_format($payroll->gross_salary, 2) }}</div>
                        </td>
                        <td>
                            <div class="summary-label">Total Deductions</div>
                            <div class="summary-value text-red">₱ {{ number_format($payroll->total_deductions, 2) }}</div>
                        </td>
                        <td>
                            <div class="summary-label">Net Pay</div>
                            <div class="summary-value text-green">₱ {{ number_format($payroll->net_salary, 2) }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Additional Info -->
            <table class="columns-table">
                <tr>
                    <td style="width: 50%;">
                        <div class="section-title">WORK HOURS SUMMARY</div>
                        <table class="breakdown-table">
                            <tr>
                                <td class="item-name">Regular Hours</td>
                                <td class="amount-cell">{{ $payroll->regular_hours ?? 160 }} hours</td>
                            </tr>
                            @if(($payroll->overtime_hours ?? 0) > 0)
                            <tr>
                                <td class="item-name">Overtime Hours</td>
                                <td class="amount-cell">{{ $payroll->overtime_hours }} hours</td>
                            </tr>
                            @endif
                            @if(($payroll->late_hours ?? 0) > 0)
                            <tr>
                                <td class="item-name">Late Hours</td>
                                <td class="amount-cell">{{ $payroll->late_hours }} hours</td>
                            </tr>
                            @endif
                            @if(($payroll->absent_days ?? 0) > 0)
                            <tr>
                                <td class="item-name">Absent Days</td>
                                <td class="amount-cell">{{ $payroll->absent_days }} days</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                    <td style="width: 50%;">
                        <div class="section-title">LEAVE BALANCE</div>
                        <table class="breakdown-table">
                            <tr>
                                <td class="item-name">Vacation Leave</td>
                                <td class="amount-cell">{{ $employee->vacation_leave_balance ?? 0 }} days</td>
                            </tr>
                            <tr>
                                <td class="item-name">Sick Leave</td>
                                <td class="amount-cell">{{ $employee->sick_leave_balance ?? 0 }} days</td>
                            </tr>
                            <tr>
                                <td class="item-name">Emergency Leave</td>
                                <td class="amount-cell">{{ $employee->emergency_leave_balance ?? 0 }} days</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Signature Area -->
            <div class="signature-area">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div class="signature-line"></div>
                            <div class="signatory-name">{{ strtoupper($employee->first_name) }} {{ strtoupper($employee->last_name) }}</div>
                            <div class="signatory-title">Employee Signature</div>
                        </td>
                        <td>
                            <div class="signature-line"></div>
                            <div class="signatory-name">{{ strtoupper($supervisor->name) }}</div>
                            <div class="signatory-title">Immediate Supervisor</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This is a computer-generated document. No signature is required.</p>
                <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
                <p>For inquiries, please contact HR Department at hr@bipsu.edu.ph or (053) 123-4567</p>
            </div>
        </div>
    </div>
</body>
</html>
