@extends('admin.layouts.app')

@section('title', 'Payroll Periods')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Header Section -->
        <x-admin.page-header
            title="Payroll Periods"
            description="Manage and track all payroll periods"
        >
            <x-slot name="actions">
                <div x-data>
                    <x-admin.action-button @click="$dispatch('open-modal-create-period')" variant="primary" icon="fas fa-plus">
                        Create New Period
                    </x-admin.action-button>
                </div>
            </x-slot>
        </x-admin.page-header>

        <!-- Filter Section -->
        <x-admin.card title="Filters" class="mb-6">
            <form action="{{ route('admin.payroll.periods.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <!-- Year Filter -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" id="year" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                        <option value="">All Years</option>
                        @foreach(range(date('Y') - 2, date('Y') + 2) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Filter -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" id="month" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
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
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Cut-off Filter -->
                <div>
                    <label for="cut_off" class="block text-sm font-medium text-gray-700 mb-1">Cut-off</label>
                    <select name="cut_off" id="cut_off" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                        <option value="">All Cut-offs</option>
                        <option value="1st" {{ request('cut_off') == '1st' ? 'selected' : '' }}>1st Cut-off</option>
                        <option value="2nd" {{ request('cut_off') == '2nd' ? 'selected' : '' }}>2nd Cut-off</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    <a href="{{ route('admin.payroll.periods.index') }}" class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md border border-gray-300 shadow-sm transition duration-150 flex items-center justify-center">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </x-admin.card>

        <!-- Table Section -->
        <x-admin.card :padding="false" class="overflow-visible">
            <x-admin.table-wrapper>
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-calendar-alt mr-2"></i>Period
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-clock mr-2"></i>Dates
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-circle-check mr-2"></i>Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periods as $period)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $period->formatted_period }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $period->start_date->diffInDays($period->end_date) + 1 }} days
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $period->cut_off_type_id == 1 ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $period->cut_off_type_id == 1 ? '1st Half' : '2nd Half' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusVariant = match($period->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        default => 'default'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ match($statusVariant) { 'success' => 'bg-green-100 text-green-800', 'warning' => 'bg-yellow-100 text-yellow-800', 'default' => 'bg-gray-100 text-gray-800' } }}">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($period->status !== 'completed')
                                        <form action="{{ route('admin.payroll.periods.update', $period) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <x-admin.action-button type="submit" variant="success" size="sm" icon="fas fa-check" iconOnly title="Mark as Completed" onclick="return confirm('Are you sure you want to mark this period as completed?')" />
                                        </form>
                                    @else
                                        <form action="{{ route('admin.payroll.periods.update', $period) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="pending">
                                            <x-admin.action-button type="submit" variant="warning" size="sm" icon="fas fa-undo" iconOnly title="Revert to Pending" onclick="return confirm('Are you sure you want to revert this period to pending status?')" />
                                        </form>
                                    @endif

                                    <x-admin.action-button href="{{ route('admin.payroll.generation.export-general-sheet', $period) }}" variant="info" size="sm" icon="fas fa-file-excel" iconOnly title="Export General Payroll Sheet" />
                                    
                                    @if($period->payrolls->count() === 0)
                                        <form action="{{ route('admin.payroll.periods.destroy', $period) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-admin.action-button type="submit" variant="danger" size="sm" icon="fas fa-trash" iconOnly title="Delete Period" onclick="return confirm('Are you sure you want to delete this period?')" />
                                        </form>
                                    @else
                                        <x-admin.action-button variant="secondary" size="sm" icon="fas fa-lock" iconOnly disabled title="Cannot delete period with payrolls" class="cursor-not-allowed opacity-50" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                <div class="flex flex-col items-center py-6">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                    <p class="font-medium text-gray-900">No payroll periods found</p>
                                    <p class="text-gray-500">Get started by creating a new period.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-admin.table-wrapper>

            @if($periods->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $periods->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>

    <!-- Create Period Modal -->
    <x-admin.modal name="create-period" title="Create New Payroll Period">
        <form action="{{ route('admin.payroll.periods.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" id="start_date" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" id="end_date" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="cut_off_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Cut Off Type <span class="text-red-500">*</span>
                    </label>
                    <select name="cut_off_type_id" id="cut_off_type_id" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                        <option value="" class="text-gray-500">Select a type...</option>
                        <option value="1">1st Half (1-15)</option>
                        <option value="2">2nd Half (16-End)</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 flex justify-end space-x-3">
                <x-admin.action-button type="button" variant="secondary" @click="show = false">
                    Cancel
                </x-admin.action-button>
                <x-admin.action-button type="submit" variant="primary">
                    Create Period
                </x-admin.action-button>
            </div>
        </form>
    </x-admin.modal>
@endsection
