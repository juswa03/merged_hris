<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 14px; font-weight: bold;">
                Civil Service Form No. 48
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">
                DAILY TIME RECORD
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: left; padding: 5px;">
                Name: <span style="font-weight: normal;">{{ strtoupper($employee->first_name . ' ' . ($employee->middle_name ? $employee->middle_name[0] . '. ' : '') . $employee->last_name) }}</span>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: left; padding: 5px;">
                Department: <span style="font-weight: normal;">{{ $employee->department->name ?? 'N/A' }}</span>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: left; padding: 5px;">
                Position: <span style="font-weight: normal;">{{ $employee->position->title ?? 'N/A' }}</span>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; padding: 5px; font-size: 14px;">
                For the month of: <span style="font-weight: bold;">{{ strtoupper($monthYear) }}</span>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="padding: 5px;"></th>
        </tr>
        <tr style="background-color: #4472C4; color: white; text-align: center;">
            <th rowspan="2" style="padding: 8px; vertical-align: middle;">DAY</th>
            <th colspan="2" style="padding: 8px;">A.M.</th>
            <th colspan="2" style="padding: 8px;">P.M.</th>
            <th rowspan="2" style="padding: 8px; vertical-align: middle;">UNDERTIME</th>
            <th rowspan="2" style="padding: 8px; vertical-align: middle;">REMARKS</th>
        </tr>
        <tr style="background-color: #4472C4; color: white; text-align: center;">
            <th style="padding: 8px;">ARRIVAL</th>
            <th style="padding: 8px;">DEPARTURE</th>
            <th style="padding: 8px;">ARRIVAL</th>
            <th style="padding: 8px;">DEPARTURE</th>
        </tr>
    </thead>
    <tbody>
        @foreach($daysInMonth as $dayData)
        <tr>
            <td style="text-align: center; padding: 5px;">{{ $dayData['day'] }}</td>
            <td style="text-align: center; padding: 5px;">{{ $dayData['am_arrival'] }}</td>
            <td style="text-align: center; padding: 5px;">{{ $dayData['am_departure'] }}</td>
            <td style="text-align: center; padding: 5px;">{{ $dayData['pm_arrival'] }}</td>
            <td style="text-align: center; padding: 5px;">{{ $dayData['pm_departure'] }}</td>
            <td style="text-align: center; padding: 5px;">{{ $dayData['undertime_hours'] }}</td>
            <td style="padding: 5px;">{{ $dayData['remarks'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5" style="text-align: right; padding: 8px; font-weight: bold;">Total Undertime:</td>
            <td style="text-align: center; padding: 8px; font-weight: bold;">{{ $totalUndertime }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="7" style="padding: 10px;"></td>
        </tr>
        <tr>
            <td colspan="7" style="padding: 5px; font-size: 11px; font-style: italic;">
                I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
            </td>
        </tr>
        <tr>
            <td colspan="7" style="padding: 20px 5px 5px 5px;">
                <div style="text-align: center;">
                    _________________________________<br>
                    <span style="font-weight: bold;">{{ strtoupper($employee->first_name . ' ' . $employee->last_name) }}</span>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="7" style="padding: 20px 5px 5px 5px;">
                VERIFIED as to the prescribed office hours:
            </td>
        </tr>
        <tr>
            <td colspan="7" style="padding: 20px 5px 5px 5px;">
                <div style="text-align: center;">
                    _________________________________<br>
                    <span style="font-weight: bold;">IN-CHARGE</span>
                </div>
            </td>
        </tr>
    </tbody>
</table>
