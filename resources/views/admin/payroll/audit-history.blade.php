@extends('layouts.app')

@section('title', 'Payroll Audit History')

@section('content')
<div class="w-full px-6 py-6 max-w-7xl mx-auto">
    <!-- Page Header -->
    <x-admin.page-header
        title="Payroll Audit History"
        description="Track all changes, approvals, and modifications to payroll records"
    >
        <x-slot name="actions">
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Payroll
            </a>
        </x-slot>
    </x-admin.page-header>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-blue-600"></i> Filter Audit Records
        </h3>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee</label>
                <select name="employee_id" id="employeeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Employees</option>
                    @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select name="action" id="actionFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Actions</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="processed">Processed</option>
                    <option value="paid">Paid</option>
                    <option value="deleted">Deleted</option>
                    <option value="regenerated">Regenerated</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select name="date_range" id="dateRangeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">Last 7 Days</option>
                    <option value="month">Last 30 Days</option>
                    <option value="quarter">Last 90 Days</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-medium flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Timeline -->
    <div class="space-y-4">
        @forelse($auditTrail ?? [] as $audit)
            <div class="bg-white rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $audit->action === 'created' ? 'bg-green-100 text-green-800' : ($audit->action === 'deleted' ? 'bg-red-100 text-red-800' : ($audit->action === 'updated' ? 'bg-yellow-100 text-yellow-800' : ($audit->action === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'))) }}">
                                <i class="fas fa-{{ $audit->action === 'created' ? 'plus' : ($audit->action === 'deleted' ? 'trash' : ($audit->action === 'updated' ? 'edit' : ($audit->action === 'approved' ? 'check' : 'circle'))) }} mr-1"></i>
                                {{ ucfirst($audit->action) }}
                            </span>
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $audit->created_at->format('M d, Y \a\t H:i') }}
                            </span>
                        </div>

                        <h4 class="text-lg font-semibold text-gray-900 mb-1">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            {{ $audit->payroll?->employee?->full_name ?? 'Unknown Employee' }}
                        </h4>

                        <p class="text-sm text-gray-600 mb-3">
                            <i class="fas fa-user-tie mr-1"></i>
                            <strong>By:</strong> {{ $audit->user?->name ?? 'System' }}
                        </p>

                        @if($audit->reason)
                            <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-comment mr-1"></i>
                                    <strong>Reason:</strong> {{ $audit->reason }}
                                </p>
                            </div>
                        @endif

                        @if($audit->changes && is_array($audit->changes) && count($audit->changes) > 0)
                            <div class="bg-gray-50 border border-gray-200 rounded p-3 overflow-x-auto">
                                <p class="text-sm font-semibold text-gray-900 mb-2">Changes:</p>
                                <table class="text-xs w-full">
                                    <thead>
                                        <tr class="border-b border-gray-300">
                                            <th class="text-left px-2 py-1 font-semibold text-gray-700">Field</th>
                                            <th class="text-left px-2 py-1 font-semibold text-gray-700">Old Value</th>
                                            <th class="text-left px-2 py-1 font-semibold text-gray-700">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($audit->changes as $field => $change)
                                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                                <td class="px-2 py-2 font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                                <td class="px-2 py-2 text-red-600">
                                                    @if(is_array($change))
                                                        {{ $change['old'] ?? 'N/A' }}
                                                    @else
                                                        {{ $change }}
                                                    @endif
                                                </td>
                                                <td class="px-2 py-2 text-green-600">
                                                    @if(is_array($change))
                                                        {{ $change['new'] ?? 'N/A' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="text-right ml-4">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-shield-alt mr-1"></i>
                            {{ $audit->ip_address ?? 'Unknown IP' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-12 text-center">
                <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 text-lg">No audit records found</p>
                <p class="text-gray-500 text-sm mt-2">Changes and actions on payroll will appear here</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(($auditTrail ?? collect())->count() > 0)
        <div class="mt-6 flex justify-center">
            {{ ($auditTrail ?? collect())->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const employeeId = document.getElementById('employeeFilter').value;
        const action = document.getElementById('actionFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;

        let url = '{{ route("payroll.audit-history") }}?';
        if (employeeId) url += `employee_id=${employeeId}&`;
        if (action) url += `action=${action}&`;
        if (dateRange && dateRange !== 'all') url += `date_range=${dateRange}`;

        window.location.href = url;
    });
</script>
@endpush

@endsection
