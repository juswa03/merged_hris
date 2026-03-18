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
            width: 150px;
            margin: 0 auto 5px auto;
            height: 30px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 15px;">
        <div style="font-size: 11px; font-weight: bold; margin-bottom: 5px;">CSC FORM No. 48</div>
        <div style="font-size: 14px; font-weight: bold; margin-bottom: 15px;">DAILY TIME RECORD</div>
    </div>

    <!-- Name Section -->
    <div style="margin-bottom: 10px;">
        <div style="border: none; border-bottom: 1px solid #000; width: 100%; padding: 2px 0; font-size: 12px; text-align: center; font-weight: bold; margin-bottom: 2px;">
            {{ strtoupper($employee->last_name . ', ' . $employee->first_name . ' ' . ($employee->middle_name ?? '')) }}
        </div>
        <div style="text-align: center; font-size: 10px; margin-top: 2px;">NAME</div>
    </div>

    <!-- Month Section -->
    <div style="margin-bottom: 15px;">
        <div style="border: none; border-bottom: 1px solid #000; width: 100%; padding: 2px 0; font-size: 11px; text-align: center; margin-bottom: 2px;">
            {{ $monthYear }}
        </div>
        <div style="text-align: center; font-size: 10px; margin-top: 2px;">MONTH</div>
    </div>

    <!-- Office Hours -->
    <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
        <tr>
            <td style="font-size: 9px; text-align: left; border: none; padding: 0; width: 40%;">
                <div>Official hours for arrival</div>
                <div>and departure</div>
            </td>
            <td style="font-size: 9px; text-align: right; border: none; padding: 0; width: 60%;">
                <div>Regular days 8:00 AM - 12:00 PM</div>
                <div>1:00 PM - 5:00 PM    Saturdays: as required</div>
            </td>
        </tr>
    </table>

    <!-- DTR Table -->
    <table class="dtr-table">
        <thead>
            <tr>
                <th rowspan="2" width="5%">Day</th>
                <th colspan="2">A.M.</th>
                <th colspan="2">P.M.</th>
                <th colspan="2">Undertime</th>
            </tr>
            <tr>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure</th>
                <th>Hrs</th>
                <th>Mins</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daysInMonth as $dayData)
            <tr>
                <td>{{ $dayData['day'] }}</td>
                <td>{{ $dayData['am_arrival'] }}</td>
                <td>{{ $dayData['am_departure'] }}</td>
                <td>{{ $dayData['pm_arrival'] }}</td>
                <td>{{ $dayData['pm_departure'] }}</td>
                <td>{{ $dayData['undertime_hours'] }}</td>
                <td>{{ $dayData['undertime_minutes'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td><strong>{{ $totalUndertimeHours }}</strong></td>
                <td><strong>{{ $totalUndertimeMinutes }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align: center; font-style: italic; font-size: 10px; margin: 5px 0;">
        Official Hours: Arrival 8:00 AM | Departure 5:00 PM
    </p>

    <div class="footer">
        <p>I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</p>
        
        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <!-- Employee Signature -->
                <td style="width: 33%; text-align: center;">
                    <div class="signature-line"></div>
                    <p><strong>{{ strtoupper($employee->first_name . ' ' . $employee->last_name) }}</strong></p>
                    <p style="font-size: 10px; margin: 0;">Employee</p>
                </td>
                
                <!-- Supervisor Signature -->
                <td style="width: 33%; text-align: center;">
                    <div class="signature-line"></div>
                    <p><strong>SUPERVISOR</strong></p>
                    <p style="font-size: 10px; margin: 0;">Immediate Supervisor</p>
                </td>
                
                <!-- HR Officer Signature -->
                <td style="width: 33%; text-align: center;">
                    <div class="signature-line"></div>
                    <p><strong>HR OFFICER</strong></p>
                    <p style="font-size: 10px; margin: 0;">Verified by (HR/Authorized Officer)</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
