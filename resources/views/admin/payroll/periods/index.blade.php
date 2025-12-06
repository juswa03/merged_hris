@extends('layouts.app')

@section('title', 'Payroll Periods')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Payroll Periods</h2>
                <p class="text-gray-600 text-sm mt-1">Manage and track all payroll periods</p>
            </div>
            <button onclick="document.getElementById('createPeriodModal').classList.remove('hidden')" class="mt-4 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white font-black text-lg py-4 px-10 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-300 flex items-center transform hover:scale-110 active:scale-95 border-2 border-blue-400 hover:border-blue-500">
                <i class="fas fa-plus mr-3 text-xl"></i> <span>Create New Period</span>
            </button>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-100">
            <form action="{{ route('payroll.periods.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <!-- Year Filter -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" id="year" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">All Years</option>
                        @foreach(range(date('Y') - 2, date('Y') + 2) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" id="month" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Cut-off Filter -->
                <div>
                    <label for="cut_off" class="block text-sm font-medium text-gray-700 mb-1">Cut-off</label>
                    <select name="cut_off" id="cut_off" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">All Cut-offs</option>
                        <option value="1st" {{ request('cut_off') == '1st' ? 'selected' : '' }}>1st Cut-off</option>
                        <option value="2nd" {{ request('cut_off') == '2nd' ? 'selected' : '' }}>2nd Cut-off</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('payroll.periods.index') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center border border-gray-300">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wide">
                                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>Period
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wide">
                                <i class="fas fa-clock mr-2 text-blue-600"></i>Dates
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wide">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>Type
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wide">
                                <i class="fas fa-circle-check mr-2 text-blue-600"></i>Status
                            </th>
                            <th class="sticky right-0 px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wide bg-gradient-to-r from-gray-50 to-gray-100 shadow-lg border-l border-gray-200">
                                <i class="fas fa-tools mr-2 text-blue-600"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($periods as $period)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-100 group-hover:bg-blue-200 transition-colors">
                                                <i class="fas fa-calendar text-blue-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-bold text-gray-900">{{ $period->formatted_period }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-sm">
                                        <p class="text-gray-900 font-medium">{{ $period->start_date->format('M d') }} <span class="text-gray-400">→</span> {{ $period->end_date->format('M d, Y') }}</p>
                                        <p class="text-gray-500 text-xs mt-1">{{ $period->start_date->diffInDays($period->end_date) + 1 }} days</p>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $period->cut_off_type_id == 1 ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        <i class="fas {{ $period->cut_off_type_id == 1 ? 'fa-calendar-days' : 'fa-hourglass-end' }} mr-2"></i>
                                        {{ $period->cut_off_type_id == 1 ? '1st Half' : '2nd Half' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $period->status === 'completed' ? 'bg-green-100 text-green-800' : ($period->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800') }}">
                                        <i class="fas 
                                            {{ $period->status === 'completed' ? 'fa-check-circle' : ($period->status === 'pending' ? 'fa-clock' : 'fa-circle') }} 
                                            mr-2"></i>
                                        <span class="capitalize">{{ $period->status }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 sticky right-0 bg-white group-hover:bg-gray-50 transition-colors duration-150 shadow-lg">
                                    <div class="flex items-center space-x-2 justify-end">
                                        @if($period->status !== 'completed')
                                            <form action="{{ route('payroll.periods.update', $period) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-green-600 hover:bg-green-700 text-white shadow-lg hover:shadow-2xl transition-all duration-200 transform hover:scale-110 active:scale-95 z-10" title="Mark as Completed" onclick="return confirm('Are you sure you want to mark this period as completed?')">
                                                    <i class="fas fa-check text-base font-bold"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('payroll.periods.update', $period) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-yellow-500 hover:bg-yellow-600 text-white shadow-lg hover:shadow-2xl transition-all duration-200 transform hover:scale-110 active:scale-95 z-10" title="Revert to Pending" onclick="return confirm('Are you sure you want to revert this period to pending status?')">
                                                    <i class="fas fa-undo text-base font-bold"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('payroll.generation.export-general-sheet', $period) }}" class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-lg hover:shadow-2xl transition-all duration-200 transform hover:scale-110 active:scale-95 z-10" title="Export General Payroll Sheet">
                                            <i class="fas fa-file-excel text-base font-bold"></i>
                                        </a>
                                        
                                        @if($period->payrolls->count() === 0)
                                            <form action="{{ route('payroll.periods.destroy', $period) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-red-600 hover:bg-red-700 text-white shadow-lg hover:shadow-2xl transition-all duration-200 transform hover:scale-110 active:scale-95 z-10" title="Delete Period" onclick="return confirm('Are you sure you want to delete this period?')">
                                                    <i class="fas fa-trash text-base font-bold"></i>
                                                </button>
                                            </form>
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-gray-400 hover:bg-gray-500 flex items-center justify-center shadow-lg cursor-not-allowed" title="Cannot delete period with payrolls">
                                                <i class="fas fa-lock text-white text-base font-bold"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                        <p class="text-gray-500 font-medium">No payroll periods found</p>
                                        <p class="text-gray-400 text-sm mt-1">Create your first payroll period to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endempty
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between">
                <div class="text-sm text-gray-600 mb-4 sm:mb-0">
                    @if($periods->count())
                        Showing <span class="font-semibold">{{ $periods->firstItem() }}</span> to <span class="font-semibold">{{ $periods->lastItem() }}</span> of <span class="font-semibold">{{ $periods->total() }}</span> periods
                    @endif
                </div>
                <div>
                    {{ $periods->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Period Modal -->
    <div id="createPeriodModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="document.getElementById('createPeriodModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200">
                <form action="{{ route('payroll.periods.store') }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="bg-blue-600 px-6 py-5 border-b border-blue-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 bg-white/20 backdrop-blur-md rounded-full">
                                <i class="fas fa-calendar-plus text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl leading-6 font-bold text-white" id="modal-title">
                                    Create New Payroll Period
                                </h3>
                                <p class="mt-1 text-blue-100 text-sm">Set up a new payroll period with start and end dates</p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="bg-white px-6 py-6">
                        <div class="space-y-5">
                            <div>
                                <label for="start_date" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-check text-blue-600 mr-2"></i>Start Date
                                </label>
                                <div class="relative">
                                    <input type="date" name="start_date" id="start_date" required class="w-full pl-4 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-600 hover:border-blue-400 transition-all duration-200 font-medium text-gray-700 bg-gray-50 focus:bg-white">
                                </div>
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-xmark text-blue-600 mr-2"></i>End Date
                                </label>
                                <div class="relative">
                                    <input type="date" name="end_date" id="end_date" required class="w-full pl-4 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-600 hover:border-blue-400 transition-all duration-200 font-medium text-gray-700 bg-gray-50 focus:bg-white">
                                </div>
                            </div>
                            <div>
                                <label for="cut_off_type_id" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-tag text-blue-600 mr-2"></i>Cut Off Type
                                </label>
                                <div class="relative">
                                    <select name="cut_off_type_id" id="cut_off_type_id" required class="w-full pl-4 pr-10 py-2.5 border-2 border-gray-200 bg-gray-50 focus:bg-white rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-600 hover:border-blue-400 transition-all duration-200 font-medium cursor-pointer text-gray-700 appearance-none">
                                        <option value="" class="text-gray-500">Select a type...</option>
                                        <option value="1">1st Half (1-15)</option>
                                        <option value="2">2nd Half (16-End)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl border border-transparent shadow-lg px-6 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105 active:scale-95">
                            <i class="fas fa-check mr-2"></i> Create Period
                        </button>
                        <button type="button" onclick="document.getElementById('createPeriodModal').classList.add('hidden')" class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center rounded-xl border-2 border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300 transform hover:scale-105 active:scale-95">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
