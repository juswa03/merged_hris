@extends('layouts.app')

@section('title', 'Daily Time Record')

@section('content')
<style>
    th {
        text-align: center;
    }

@media print {
    body * {
        visibility: hidden;
    }

    #dtr-area, #dtr-area * {
        visibility: visible;
    }

    #dtr-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    /* Hide the print button */
    .no-print {
        display: none !important;
    }
}
</style>


<div class="container mx-auto px-4 py-6" >
    {{-- Month Picker --}}
    <div class="flex flex-col md:flex-row justify-between mb-4 gap-2 md:gap-0">
        <form method="GET" action="{{ route('dtr.index') }}" class="flex items-center gap-2">
            <label for="month" class="text-sm font-medium">Select Month:</label>
            <input type="month" id="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                   class="border border-gray-300 rounded px-2 py-1 text-sm">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                View
            </button>
        </form>

        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow">
            <i class="fas fa-print mr-2"></i> Print DTR
        </button>
    </div>
    <div id="dtr-area">
        <div class="bg-white shadow rounded-lg overflow-x-auto print:border print:p-4">
            <p class="text-sm text-gray-600" style="padding: 10px">Civil Service Form No. 48</p>
        <div class="p-6 border-b text-center">
            <h2 class="text-xl font-bold uppercase mb-2">Daily Time Record</h2>
            <p class="text-sm text-gray-600">{{ $monthYear }}</p>
            <p class="text-sm text-gray-600">Name: <span class="underline font-semibold">{{ $employee->first_name }} {{ $employee->last_name }}</span></p>
        </div>

        {{-- DTR Table --}}
        <div class="p-6">
            <table class="min-w-full text-sm text-left text-gray-700 border print:text-xs">
                <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                    <tr>
                        <th class="px-2 py-1 border" rowspan="2">Day</th>
                        <th class="px-2 py-1 border" colspan="2">A.M.</th>
                        <th class="px-2 py-1 border" colspan="2">P.M.</th>
                        <th class="px-2 py-1 border" colspan="2">UNDERTIME</th>
                        <th class="px-2 py-1 border" colspan="2">WORK HOURS</th>
                        <th class="px-2 py-1 border" rowspan="2">Remarks</th>
                    </tr>
                    <tr>
                        <th class="px-2 py-1 border">Arrival</th>
                        <th class="px-2 py-1 border">Departure</th>
                        <th class="px-2 py-1 border">Arrival</th>
                        <th class="px-2 py-1 border">Departure</th>
                        <th class="px-2 py-1 border">Hours</th>
                        <th class="px-2 py-1 border">Minutes</th>
                        <th class="px-2 py-1 border">Hours</th>
                        <th class="px-2 py-1 border">Minutes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dtrEntries as $entry)
                    <tr class="hover:bg-gray-100 {{ $entry->is_weekend ? 'bg-gray-100 text-gray-500 italic' : '' }}">
                        <td class="px-2 py-1 border text-center">{{ \Carbon\Carbon::parse($entry->dtr_date)->format('d') }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->am_arrival ?? '-' }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->am_departure ?? '-' }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->pm_arrival ?? '-' }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->pm_departure ?? '-' }}</td>
                        <td class="px-2 py-1 border text-center">{{ floor($entry->undertime_minutes / 60) }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->undertime_minutes % 60 }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->total_hours }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->total_minutes }}</td>
                        <td class="px-2 py-1 border text-center">{{ $entry->remarks ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="5" class="px-2 py-1 border text-right">TOTAL</td>
                        <td class="px-2 py-1 border text-center">{{ floor($totalUndertime / 60) }}</td>
                        <td class="px-2 py-1 border text-center">{{ $totalUndertime % 60 }}</td>
                        <td class="px-2 py-1 border text-center">{{ floor($totalMinutes / 60) }}</td>
                        <td class="px-2 py-1 border text-center">{{ $totalMinutes % 60 }}</td>
                        <td class="px-2 py-1 border"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-8 text-sm leading-relaxed print:mt-6">
                <p>I CERTIFY on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.</p>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-6 text-center">
                <div>
                    <p class="font-semibold underline">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                    <p class="text-sm">Employee</p>
                </div>
                <div>
                    <p class="font-semibold underline">{{ $certifiedBy->name }}</p>
                    <p class="text-sm">In-Charge</p>
                </div>
            </div>
        </div>
    </div>
    </div>
    
</div>
@endsection
