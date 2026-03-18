@extends('finance.layouts.app')

@section('title', 'Travel Requests Pending Your Approval')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Travel Requests - Finance Officer Review</h1>
        <p class="text-gray-600">Review travel requests and approve/reject allotment available stage</p>
    </div>

    <!-- Pending Count Card -->
    @if($pendingCount > 0)
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-orange-600 text-2xl mr-4"></i>
                <div>
                    <h3 class="font-semibold text-orange-900">{{ $pendingCount }} Pending Request{{ $pendingCount !== 1 ? 's' : '' }}</h3>
                    <p class="text-sm text-orange-700">Awaiting your approval decision</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="under_review">Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Employee Name</label>
                <input type="text" id="searchInput" placeholder="Employee name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
        @if($travels->isEmpty())
            <div class="p-8 text-center">
                <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No travel requests to review</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-300">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Employee</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Destination</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Travel Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Travel Type</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-800">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travels as $travel)
                            @php
                                $recommendingApproval = $travel->approvals->where('approval_type', 'recommending_approval')->first();
                                $isPending = $travel->status === 'pending';
                                $isApproved = $recommendingApproval && $recommendingApproval->status === 'approved';
                                $isRejected = $travel->status === 'rejected';
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-gray-50 travel-row"
                                data-status="{{ $travel->status }}"
                                data-employee="{{ strtolower($travel->user->first_name . ' ' . $travel->user->last_name) }}">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $travel->user->first_name }} {{ $travel->user->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $travel->user->email ?? 'No email' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900">{{ $travel->destination }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900">{{ $travel->inclusive_date_of_travel->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($travel->travel_type === 'official_time') bg-blue-100 text-blue-800
                                        @elseif($travel->travel_type === 'official_business') bg-green-100 text-green-800
                                        @elseif($travel->travel_type === 'personal_abroad') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $travel->travel_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($travel->status === 'pending') bg-orange-100 text-orange-800
                                        @elseif($travel->status === 'under_review') bg-blue-100 text-blue-800
                                        @elseif($travel->status === 'approved') bg-green-100 text-green-800
                                        @elseif($travel->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $travel->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('finance.travel.show', $travel) }}" 
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                                        <i class="fas fa-eye mr-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $travels->links() }}
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
    const travelRows = document.querySelectorAll('.travel-row');

    function filterTravels() {
        const statusValue = statusFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();

        travelRows.forEach(row => {
            const status = row.getAttribute('data-status').toLowerCase();
            const employee = row.getAttribute('data-employee');

            const statusMatch = !statusValue || status.includes(statusValue);
            const searchMatch = !searchValue || employee.includes(searchValue);

            row.style.display = (statusMatch && searchMatch) ? '' : 'none';
        });
    }

    statusFilter.addEventListener('change', filterTravels);
    searchInput.addEventListener('keyup', filterTravels);
    resetBtn.addEventListener('click', function() {
        statusFilter.value = '';
        searchInput.value = '';
        filterTravels();
    });
});
</script>
@endpush
@endsection
