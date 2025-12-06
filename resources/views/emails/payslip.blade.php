<!DOCTYPE html>
<html>
<head>
    <title>Payslip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payslip Notification</h2>
        </div>
        <div class="content">
            <p>Dear {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }},</p>
            
            <p>Please find attached your payslip for the period: <strong>{{ $payroll->payrollPeriod->period_name }}</strong>.</p>
            
            <p><strong>Summary:</strong></p>
            <ul>
                <li><strong>Period:</strong> {{ $payroll->payrollPeriod->start_date->format('M d, Y') }} - {{ $payroll->payrollPeriod->end_date->format('M d, Y') }}</li>
                <li><strong>Net Pay:</strong> ₱{{ number_format($payroll->net_pay, 2) }}</li>
            </ul>
            
            <p>If you have any questions regarding your payslip, please contact the HR department.</p>
            
            <p>Best regards,<br>
            Biliran Province State University HR Team</p>
        </div>
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>
