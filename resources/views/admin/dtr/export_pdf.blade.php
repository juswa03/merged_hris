<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTR Export — {{ $monthYear }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            background: #fff;
        }

        .page-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .page-header h1 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .page-header p {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .employee-block {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }

        .employee-block h2 {
            font-size: 11px;
            font-weight: bold;
            background: #1e3a5f;
            color: #fff;
            padding: 5px 8px;
            border-radius: 3px 3px 0 0;
        }

        .employee-block .meta {
            background: #f0f4f8;
            padding: 4px 8px;
            font-size: 9px;
            color: #555;
            border-left: 1px solid #d0d9e2;
            border-right: 1px solid #d0d9e2;
            border-bottom: 1px solid #d0d9e2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        thead tr {
            background: #2d5f8a;
            color: #fff;
        }

        thead th {
            padding: 5px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #1e3a5f;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #dde4eb;
        }

        tbody tr:nth-child(even) {
            background: #f7f9fc;
        }

        tbody tr.weekend {
            background: #fef9ec;
        }

        tbody tr.holiday {
            background: #fef0f0;
        }

        tbody td {
            padding: 4px 6px;
            text-align: center;
            border: 1px solid #dde4eb;
        }

        tbody td.date-col {
            text-align: left;
            font-weight: 600;
            color: #1e3a5f;
        }

        tfoot tr {
            background: #e8f0f8;
            font-weight: bold;
        }

        tfoot td {
            padding: 5px 6px;
            border: 1px solid #c5d5e5;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-weekend { background: #fef3c7; color: #92400e; }
        .badge-holiday { background: #fee2e2; color: #991b1b; }

        .summary {
            display: flex;
            gap: 8px;
            margin-top: 4px;
        }

        .summary-item {
            background: #f0f4f8;
            border: 1px solid #d0d9e2;
            border-radius: 3px;
            padding: 3px 8px;
            font-size: 9px;
        }

        .summary-item span {
            font-weight: bold;
            color: #1e3a5f;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="page-header">
        <h1>Daily Time Record (DTR)</h1>
        <p>Period: {{ $monthYear }} &nbsp;&bull;&nbsp; Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    @forelse($grouped as $employeeId => $entries)
        @php
            $employee = $entries->first()->employee;
            $totalHoursWorked = $entries->sum(fn($e) => ($e->total_hours * 60) + $e->total_minutes);
            $totalUndertime = $entries->sum('under_time_minutes');
            $daysPresent = $entries->filter(fn($e) => !$e->is_weekend && ($e->am_arrival || $e->pm_arrival))->count();
        @endphp

        <div class="employee-block">
            <h2>{{ $employee->full_name ?? 'Unknown' }}</h2>
            <div class="meta">
                <strong>Employee ID:</strong> {{ $employee->employee_id ?? $employeeId }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Position:</strong> {{ $employee->position->title ?? 'N/A' }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>AM Arrival</th>
                        <th>AM Departure</th>
                        <th>PM Arrival</th>
                        <th>PM Departure</th>
                        <th>Total Hours</th>
                        <th>Undertime (min)</th>
                        <th>Remarks</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        @php
                            $rowClass = '';
                            if ($entry->is_weekend) $rowClass = 'weekend';
                            elseif ($entry->is_holiday) $rowClass = 'holiday';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="date-col">{{ \Carbon\Carbon::parse($entry->dtr_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->dtr_date)->format('D') }}</td>
                            <td>{{ $entry->am_arrival ?? '—' }}</td>
                            <td>{{ $entry->am_departure ?? '—' }}</td>
                            <td>{{ $entry->pm_arrival ?? '—' }}</td>
                            <td>{{ $entry->pm_departure ?? '—' }}</td>
                            <td>
                                @if($entry->total_hours || $entry->total_minutes)
                                    {{ $entry->total_hours }}h {{ $entry->total_minutes }}m
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $entry->under_time_minutes ? $entry->under_time_minutes . ' min' : '—' }}</td>
                            <td>
                                {{ $entry->remarks ?? '' }}
                                @if($entry->is_weekend)
                                    <span class="badge badge-weekend">Weekend</span>
                                @elseif($entry->is_holiday)
                                    <span class="badge badge-holiday">Holiday</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($entry->status ?? '—') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right; padding-right:10px;">Totals:</td>
                        <td>{{ floor($totalHoursWorked / 60) }}h {{ $totalHoursWorked % 60 }}m</td>
                        <td>{{ $totalUndertime }} min</td>
                        <td colspan="2">Days Present: {{ $daysPresent }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <p style="text-align:center; color:#888; margin-top:40px;">No DTR records found for {{ $monthYear }}.</p>
    @endforelse

    <div class="footer">
        Baybay City Polytechnic State University — HRIS &nbsp;&bull;&nbsp; DTR Export &nbsp;&bull;&nbsp; {{ $monthYear }}
    </div>

</body>
</html>
