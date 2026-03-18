@extends('admin.layouts.app')

@section('title', 'Payroll Audit History')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Payroll Audit History"
        description="Track all changes, approvals, and modifications to payroll records"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.payroll.index') }}" variant="secondary" icon="fas fa-arrow-left">
                Back to Payroll
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Filters -->
    <x-admin.card title="Filter Audit Records" class="mb-6">
        <form action="{{ route('admin.payroll.audit-history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select name="employee_id" id="employee_id" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                    <option value="">All Employees</option>
                    @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                <select name="action" id="action" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                    <option value="">All Actions</option>
                    @foreach(['created', 'updated', 'approved', 'rejected', 'processed', 'paid', 'deleted', 'regenerated'] as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select name="date_range" id="date_range" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm sm:text-sm">
                    <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>Last 90 Days</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 flex items-center justify-center">
                    <i class="fas fa-filter mr-2"></i> Search
                </button>
                <a href="{{ route('admin.payroll.audit-history') }}" class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md border border-gray-300 shadow-sm transition duration-150 flex items-center justify-center">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </x-admin.card>

    <!-- Audit Timeline -->
    <div class="space-y-4">
        @forelse($auditTrail ?? [] as $audit)
            <x-admin.card :padding="false" class="hover:shadow-lg transition duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                @php
                                    $actionColors = [
                                        'created' => 'bg-green-100 text-green-800',
                                        'deleted' => 'bg-red-100 text-red-800',
                                        'updated' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-blue-100 text-blue-800',
                                        'processed' => 'bg-purple-100 text-purple-800',
                                        'paid' => 'bg-emerald-100 text-emerald-800',
                                        'generated' => 'bg-indigo-100 text-indigo-800',
                                        'regenerated' => 'bg-indigo-100 text-indigo-800',
                                    ];
                                    
                                    $actionIcons = [
                                        'created' => 'plus',
                                        'deleted' => 'trash',
                                        'updated' => 'edit',
                                        'approved' => 'check',
                                        'processed' => 'cogs',
                                        'paid' => 'money-bill-wave',
                                        'generated' => 'file-invoice',
                                        'regenerated' => 'sync',
                                    ];
                                    
                                    $colorClass = $actionColors[$audit->action] ?? 'bg-gray-100 text-gray-800';
                                    $iconClass = $actionIcons[$audit->action] ?? 'circle';
                                @endphp
                                
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                    <i class="fas fa-{{ $iconClass }} mr-1.5"></i>
                                    {{ ucfirst($audit->action) }}
                                </span>
                                <span class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-calendar-alt mr-1.5 opacity-75"></i>
                                    {{ $audit->created_at->format('M d, Y') }} at {{ $audit->created_at->format('h:i A') }}
                                </span>
                            </div>

                            <h4 class="text-base font-semibold text-gray-900 mb-1 flex items-center">
                                <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2 text-blue-600 text-xs shadow-sm">
                                    <i class="fas fa-user-tag"></i>
                                </span>
                                {{ $audit->payroll?->employee?->full_name ?? 'Unknown Employee' }}
                            </h4>

                            <p class="text-sm text-gray-600 mb-3 ml-10">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i>
                                <span class="font-medium text-gray-700">Action by:</span> {{ $audit->user?->name ?? 'System' }}
                            </p>

                            @if($audit->reason)
                                <div class="ml-10 bg-blue-50 border border-blue-100 rounded-lg p-3 mb-3 text-sm text-blue-800">
                                    <strong class="font-semibold block mb-1"><i class="fas fa-comment-alt mr-1"></i> Reason:</strong> 
                                    {{ $audit->reason }}
                                </div>
                            @endif

                            @if($audit->changes && is_array($audit->changes) && count($audit->changes) > 0)
                                <div class="ml-10 bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="px-4 py-2 bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Changes applied
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="bg-gray-50">
                                                    <th scope="col" class="text-left px-4 py-2 font-medium text-gray-600 border-b border-gray-200 w-1/3">Field</th>
                                                    <th scope="col" class="text-left px-4 py-2 font-medium text-gray-600 border-b border-gray-200 w-1/3">Old Value</th>
                                                    <th scope="col" class="text-left px-4 py-2 font-medium text-gray-600 border-b border-gray-200 w-1/3">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 bg-white">
                                                @foreach($audit->changes as $field => $change)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-4 py-2 font-medium text-gray-800 border-r border-gray-100">
                                                            {{ ucwords(str_replace('_', ' ', $field)) }}
                                                        </td>
                                                        <td class="px-4 py-2 text-red-600 border-r border-gray-100 bg-red-50/30">
                                                            <div class="flex items-center">
                                                                <i class="fas fa-minus-circle mr-2 text-xs opacity-50"></i>
                                                                @if(is_array($change))
                                                                    {{ $change['old'] ?? 'N/A' }}
                                                                @else
                                                                    {{ $change }}
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-2 text-green-600 bg-green-50/30">
                                                            <div class="flex items-center">
                                                                <i class="fas fa-plus-circle mr-2 text-xs opacity-50"></i>
                                                                @if(is_array($change))
                                                                    {{ $change['new'] ?? 'N/A' }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="text-right ml-4 flex flex-col items-end">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200" title="IP Address">
                                <i class="fas fa-laptop-code mr-1.5"></i>
                                {{ $audit->ip_address ?? 'Unknown IP' }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-admin.card>
        @empty
            <x-admin.empty-state
                icon="fas fa-history"
                title="No audit records found"
                message="Changes and actions on payroll records will appear here as they happen."
            />
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if(($auditTrail ?? collect())->count() > 0)
        <div class="mt-6">
            {{ ($auditTrail ?? collect())->links() }}
        </div>
    @endif
</div>

{{-- Standard scripts are handled by the layout --}}
@endsection
