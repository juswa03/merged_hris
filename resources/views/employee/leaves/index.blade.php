@extends('employee.layouts.app')

@section('title', 'My Leave Applications')

@section('content')
<div class="mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Leave Applications</h1>
                <p class="text-gray-600 mt-2">Manage and track your leave requests</p>
            </div>
            <a href="{{ route('employees.leaves.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition flex items-center">
                <i class="fas fa-plus mr-2"></i>New Leave Application
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="filter_status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <label for="filter_leave_type" class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select id="filter_leave_type" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach(App\Models\Leave::getLeaveTypes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" id="filter_date_from" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="filter_date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" id="filter_date_to" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Applications</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $leaves->count() }}</p>
                </div>
                <i class="fas fa-file-alt text-blue-300 text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow-md p-6 border-l-4 border-yellow-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending</p>
                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $leaves->where('status', 'pending')->count() }}</p>
                </div>
                <i class="fas fa-hourglass-half text-yellow-300 text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Approved</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $leaves->where('status', 'approved')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-green-300 text-4xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-md p-6 border-l-4 border-red-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Rejected</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $leaves->where('status', 'rejected')->count() }}</p>
                </div>
                <i class="fas fa-times-circle text-red-300 text-4xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($leaves->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Filing Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Leave Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Days
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($leaves as $leave)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 font-medium">
                                        {{ $leave->filing_date->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $leave->getLeaveTypeDisplay() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $leave->start_date->format('M d') }}
                                    @if($leave->duration_type === 'multiple_days')
                                        — {{ $leave->end_date->format('M d, Y') }}
                                    @else
                                        {{ $leave->start_date->format(', Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $leave->days ?? 1 }} days
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($leave->status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-hourglass-half mr-1"></i> Pending
                                        </span>
                                    @elseif($leave->status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Approved
                                        </span>
                                    @elseif($leave->status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('employees.leaves.show', $leave->id) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($leave->status === 'pending')
                                            <a href="{{ route('employees.leaves.edit', $leave->id) }}" 
                                               class="text-amber-600 hover:text-amber-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($leave->status === 'pending')
                                            <form action="{{ route('employees.leaves.destroy', $leave->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure you want to delete this leave application?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4 inline-block"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leave Applications Yet</h3>
                <p class="text-gray-600 mb-6">You haven't submitted any leave applications yet.</p>
                <a href="{{ route('employees.leaves.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create Your First Leave Application
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Filter functionality (can be enhanced with AJAX)
document.getElementById('filter_status')?.addEventListener('change', function() {
    // Implement filtering logic here
});
</script>
@endpush
@endsection
