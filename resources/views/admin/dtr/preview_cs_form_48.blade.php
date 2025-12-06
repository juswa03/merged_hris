<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR Preview - CS Form 48</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            background-color: white;
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .form-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .main-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-row {
            margin: 4px 0;
            font-size: 11px;
        }
        .info-row strong {
            font-weight: bold;
        }
        .dtr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .dtr-table th, .dtr-table td {
            border: 1px solid #000;
            padding: 2px;
            text-align: center;
        }
        .dtr-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .certification {
            margin-top: 10px;
            font-size: 10px;
            font-style: italic;
            text-align: justify;
        }
        .signature-section {
            margin-top: 20px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 15px auto 2px;
        }
        .actions {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background-color: #4472C4;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #365899;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .info-badge {
            background-color: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        /* Two column layout */
        .columns-container {
            width: 100%;
            margin-top: 10px;
        }
        .column {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .column-left {
            margin-right: 2%;
        }
        .column-right {
            margin-left: 2%;
        }
    </style>
</head>
<body>
    @if(!isset($isPdf) || !$isPdf)
    <div class="actions">
        <a href="{{ route('dtr.export-cs-form-48', ['employee' => $employee->id, 'month' => request('month', now()->month), 'year' => request('year', now()->year)]) }}"
           class="btn btn-success">
            ⬇️ Download PDF File
        </a>
        <a href="{{ route('dtr.show', ['employee' => $employee->id]) }}" class="btn">
            ⬅️ Back to DTR
        </a>
    </div>
    <div class="container" style="max-width: 900px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div class="info-badge">
            📋 This is a preview. Click "Download PDF File" above to export.
        </div>
    @else
    <div class="container">
    @endif

        <div class="header">
            <div class="form-title">Civil Service Form No. 48</div>
            <div class="main-title">DAILY TIME RECORD</div>
        </div>

        <div class="info-row">
            <strong>Name:</strong> {{ strtoupper($employee->first_name . ' ' . ($employee->middle_name ? $employee->middle_name[0] . '. ' : '') . $employee->last_name) }}
        </div>
        <div class="info-row">
            <strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}
        </div>
        <div class="info-row">
            <strong>Position:</strong> {{ $employee->position->title ?? 'N/A' }}
        </div>
        <div class="info-row" style="text-align: center; margin: 10px 0; font-size: 12px;">
            <strong>For the month of:</strong> {{ strtoupper($monthYear) }}
        </div>

        @php
            $half = ceil(count($daysInMonth) / 2);
            $firstHalf = array_slice($daysInMonth, 0, $half);
            $secondHalf = array_slice($daysInMonth, $half);
        @endphp

        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="width: 49%; vertical-align: top; padding: 0; border: none;">
                    <table class="dtr-table">
                        <thead>
                            <tr>
                                <th rowspan="2" width="10%">DAY</th>
                                <th colspan="2">A.M.</th>
                                <th colspan="2">P.M.</th>
                                <th rowspan="2">UNDERTIME</th>
                            </tr>
                            <tr>
                                <th>ARR</th>
                                <th>DEP</th>
                                <th>ARR</th>
                                <th>DEP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($firstHalf as $dayData)
                            <tr>
                                <td>{{ $dayData['day'] }}</td>
                                <td>{{ $dayData['am_arrival'] }}</td>
                                <td>{{ $dayData['am_departure'] }}</td>
                                <td>{{ $dayData['pm_arrival'] }}</td>
                                <td>{{ $dayData['pm_departure'] }}</td>
                                <td>{{ $dayData['undertime_hours'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                <td style="width: 2%; border: none;"></td>
                <td style="width: 49%; vertical-align: top; padding: 0; border: none;">
                    <table class="dtr-table">
                        <thead>
                            <tr>
                                <th rowspan="2" width="10%">DAY</th>
                                <th colspan="2">A.M.</th>
                                <th colspan="2">P.M.</th>
                                <th rowspan="2">UNDERTIME</th>
                            </tr>
                            <tr>
                                <th>ARR</th>
                                <th>DEP</th>
                                <th>ARR</th>
                                <th>DEP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secondHalf as $dayData)
                            <tr>
                                <td>{{ $dayData['day'] }}</td>
                                <td>{{ $dayData['am_arrival'] }}</td>
                                <td>{{ $dayData['am_departure'] }}</td>
                                <td>{{ $dayData['pm_arrival'] }}</td>
                                <td>{{ $dayData['pm_departure'] }}</td>
                                <td>{{ $dayData['undertime_hours'] }}</td>
                            </tr>
                            @endforeach
                            <!-- Fill empty rows if needed to match height -->
                            @for($i = 0; $i < (count($firstHalf) - count($secondHalf)); $i++)
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin-top: 10px; font-size: 11px; font-weight: bold; text-align: right;">
            Total Undertime: {{ $totalUndertime }}
        </div>

        <div class="certification">
            I certify on my honor that the above is a true and correct report of the hours of work performed,
            record of which was made daily at the time of arrival and departure from office.
        </div>

        <div class="signature-section">
            <div class="signature-line"></div>
            <strong>{{ strtoupper($employee->first_name . ' ' . $employee->last_name) }}</strong>
        </div>

        <div style="margin-top: 20px; font-size: 11px; text-align: center;">
            VERIFIED as to the prescribed office hours:
        </div>

        <div class="signature-section">
            <div class="signature-line"></div>
            <strong>IN-CHARGE</strong>
        </div>
    </div>
</body>
</html>
