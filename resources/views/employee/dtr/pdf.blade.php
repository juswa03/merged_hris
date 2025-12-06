<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DTR - {{ $monthYear }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2, .header h3 {
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .employee-info {
            margin-bottom: 10px;
            width: 100%;
        }
        .employee-info td {
            padding: 2px;
        }
        .dtr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .dtr-table th, .dtr-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }
        .dtr-table th {
            background-color: #f0f0f0;
        }
        .weekend {
            background-color: #f9f9f9;
            font-style: italic;
        }
        .footer {
            margin-top: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 30px auto 5px auto;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td style="width: 30%; text-align: left; vertical-align: top;">
                <p style="font-size: 9px; margin: 0;">Civil Service Form No. 48</p>
            </td>
            <td style="width: 40%; text-align: center; vertical-align: top;">
                <h3 style="margin: 0; padding: 0;">DAILY TIME RECORD</h3>
                <p style="margin: 2px 0; font-size: 10px;">For the month of <strong>{{ $monthYear }}</strong></p>
            </td>
            <td style="width: 30%;"></td>
        </tr>
    </table>

    <table class="employee-info">
        <tr>
            <td width="15%"><strong>Name:</strong></td>
            <td style="border-bottom: 1px solid #000;">{{ strtoupper($employee->last_name) }}, {{ strtoupper($employee->first_name) }} {{ strtoupper($employee->middle_name) }}</td>
        </tr>
        <tr>
            <td><strong>Position:</strong></td>
            <td style="border-bottom: 1px solid #000;">{{ $employee->position->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Office:</strong></td>
            <td style="border-bottom: 1px solid #000;">{{ $employee->department->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <p style="text-align: center; font-style: italic; font-size: 10px; margin: 5px 0;">
        Official Hours: Arrival 8:00 AM | Departure 5:00 PM
    </p>

    <table class="dtr-table">
        <thead>
            <tr>
                <th rowspan="2" width="5%">Day</th>
                <th colspan="2">A.M.</th>
                <th colspan="2">P.M.</th>
                <th colspan="2">Undertime</th>
                <th colspan="2">Overtime</th>
            </tr>
            <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Hrs</th>
                <th>Mins</th>
                <th>Hrs</th>
                <th>Mins</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dtrEntries as $entry)
            @php
                $isWeekend = $entry->is_weekend;
                $rowClass = $isWeekend ? 'weekend' : '';
            @endphp
            <tr class="{{ $rowClass }}">
                <td>{{ \Carbon\Carbon::parse($entry->dtr_date)->format('d') }}</td>
                
                <!-- AM -->
                <td>{{ $entry->am_arrival ?? '' }}</td>
                <td>{{ $entry->am_departure ?? '' }}</td>
                
                <!-- PM -->
                <td>{{ $entry->pm_arrival ?? '' }}</td>
                <td>{{ $entry->pm_departure ?? '' }}</td>
                
                <!-- Undertime -->
                <td>{{ $entry->under_time_minutes > 0 ? floor($entry->under_time_minutes / 60) : '' }}</td>
                <td>{{ $entry->under_time_minutes > 0 ? ($entry->under_time_minutes % 60) : '' }}</td>

                <!-- Overtime -->
                <td>{{ $entry->over_time_hours > 0 ? $entry->over_time_hours : '' }}</td>
                <td>{{ $entry->over_time_minutes > 0 ? $entry->over_time_minutes : '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td><strong>{{ floor($totalUndertime / 60) }}</strong></td>
                <td><strong>{{ $totalUndertime % 60 }}</strong></td>
                <td><strong>{{ $totalOvertimeHours ?? 0 }}</strong></td>
                <td><strong>{{ $totalOvertimeMinutes ?? 0 }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</p>
        
        <div class="text-center">
            <div class="signature-line"></div>
            <p><strong>{{ strtoupper($employee->first_name) }} {{ strtoupper($employee->last_name) }}</strong><br>Employee</p>
        </div>

        <div class="text-center" style="margin-top: 20px;">
            <p>Verified as to the prescribed office hours:</p>
            <div class="signature-line"></div>
            <p><strong>{{ strtoupper($certifiedBy->name ?? 'SUPERVISOR') }}</strong><br>Immediate Supervisor</p>
        </div>

        <div class="text-center" style="margin-top: 20px;">
            <div class="signature-line"></div>
            <p><strong>{{ strtoupper($verifiedBy->name ?? 'HR OFFICER') }}</strong><br>Verified by (HR/Authorized Officer)</p>
        </div>
    </div>
</body>
</html>
