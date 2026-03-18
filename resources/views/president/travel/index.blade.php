@extends('president.layouts.app')

@section('title', 'Travel Requests Pending Final Approval')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Travel Requests - University President Review</h1>
        <p class="text-gray-600">Final approval of travel requests - completing the approval chain</p>
    </div>

    <!-- Pending Count Card -->
    @if($pendingCount > 0)
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-crown text-purple-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-purple-900">{{ $pendingCount }} Request{{ $pendingCount !== 1 ? 's' : '' }} Awaiting Final Approval</h3>
                    <p class="text-sm text-purple-700">Ready for University President review and signature</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="under_review">Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Employee Name</label>
                <input type="text" id="searchInput" placeholder="Employee name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <div class="flex items-end">
                <button id="resetBtn" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-redo mr-2"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Travel Requests Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($travels->count() > 0)
            <table class="w-full">
                <thead class="bg-purple-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Employee</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Destination</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Travel Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Travel Type</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($travels as $travel)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-semibold">{{ $travel->user->first_name }} {{ $travel->user->last_name }}</div>
                                <div class="text-xs text-gray-600">{{ $travel->user->designation }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $travel->destination }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($travel->duration_type === 'single_day')
                                    {{ $travel->inclusive_date_of_travel->format('M d, Y') }}
                                @else
                                    {{ $travel->start_date->format('M d') }} - {{ $travel->end_date->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">
                                {{ str_replace('_', ' ', $travel->travel_type) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($travel->status === 'pending')
                                    <span class="inline-block bg-orange-100 text-orange-800 text-xs px-3 py-1 rounded-full font-medium">
                                        <i class="fas fa-hourglass-start mr-1"></i> Pending
                                    </span>
                                @elseif($travel->status === 'approved')
                                    <span class="inline-block bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </span>
                                @elseif($travel->status === 'rejected')
                                    <span class="inline-block bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @else
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ ucfirst($travel->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('president.travel.show', $travel) }}" 
                                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition inline-block">
                                    <i class="fas fa-eye mr-2"></i> Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t">
                {{ $travels->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-inbox text-gray-300 text-5xl mb-4 block"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Pending Requests</h3>
                <p class="text-gray-600">All travel requests have been processed or there are none awaiting your final approval.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const resetBtn = document.getElementById('resetBtn');
    const tableRows = document.querySelectorAll('tbody tr');

    function filterTable() {
        const status = statusFilter.value.toLowerCase();
        const search = searchInput.value.toLowerCase();

        tableRows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
            const employeeCell = row.querySelector('td:nth-child(1)').textContent.toLowerCase();

            const matchesStatus = status === '' || statusCell.includes(status);
            const matchesSearch = search === '' || employeeCell.includes(search);

            row.style.display = matchesStatus && matchesSearch ? '' : 'none';
        });
    }

    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('keyup', filterTable);

    resetBtn.addEventListener('click', function() {
        statusFilter.value = '';
        searchInput.value = '';
        filterTable();
    });
});
</script>
@endpush
@endsection
