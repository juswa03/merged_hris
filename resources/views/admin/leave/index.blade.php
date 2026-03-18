@extends('admin.layouts.app')

@section('title', 'Leave Applications Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Leave Applications Management"
        description="Manage and process all employee leave applications"
    />

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Pending Certification" :value="$stats['pending'] ?? 0" icon="fas fa-clipboard-list" gradientFrom="yellow-500" gradientTo="yellow-600"/>
        <x-admin.gradient-stat-card title="Pending Recommendation" :value="$stats['pending_recommendation'] ?? 0" icon="fas fa-check-double" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Approved" :value="$stats['approved'] ?? 0" icon="fas fa-check-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Rejected" :value="$stats['rejected'] ?? 0" icon="fas fa-times-circle" gradientFrom="red-500" gradientTo="red-600"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card title="Filters" class="mb-6">
        <form method="GET" action="{{ route('admin.leave.index') }}" class="flex flex-col lg:flex-row gap-3">
            <select name="status" class="block w-full lg:w-52 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Certification</option>
                <option value="pending_recommendation" {{ request('status') === 'pending_recommendation' ? 'selected' : '' }}>Pending Recommendation</option>
                <option value="pending_president_approval" {{ request('status') === 'pending_president_approval' ? 'selected' : '' }}>Pending President Approval</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="type" class="block w-full lg:w-52 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Types</option>
                @foreach(App\Models\Leave::getLeaveTypes() as $value => $label)
                    <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="department" class="block w-full lg:w-52 py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">All Departments</option>
                @foreach($departments as $id => $name)
                    <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter">Filter</x-admin.action-button>
            <x-admin.action-button href="{{ route('admin.leave.index') }}" variant="secondary" icon="fas fa-times">Reset</x-admin.action-button>
        </form>
    </x-admin.card>

    <!-- Applications Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $leave->user->first_name }} {{ $leave->user->last_name }}</div>
                        <div class="text-xs text-gray-500">{{ $leave->department }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-admin.badge variant="info">{{ $leave->getLeaveTypeDisplay() }}</x-admin.badge>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $leave->start_date->format('M d, Y') }}
                        @if($leave->end_date && $leave->end_date != $leave->start_date)
                            — {{ $leave->end_date->format('M d, Y') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $leave->days ?? 1 }} days</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($leave->status === 'pending')
                            <x-admin.badge variant="warning">Pending Certification</x-admin.badge>
                        @elseif($leave->status === 'pending_recommendation')
                            <x-admin.badge variant="info">Pending Recommendation</x-admin.badge>
                        @elseif($leave->status === 'pending_president_approval')
                            <x-admin.badge variant="primary">Pending President Approval</x-admin.badge>
                        @elseif($leave->status === 'approved')
                            <x-admin.badge variant="success">Approved</x-admin.badge>
                        @elseif($leave->status === 'rejected')
                            <x-admin.badge variant="danger">Rejected</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex justify-center gap-2">
                            <x-admin.action-button :href="route('admin.leave.show', $leave)" variant="info" icon="fas fa-eye" size="sm">View</x-admin.action-button>
                            @if($leave->status === 'pending')
                                <x-admin.action-button :href="route('admin.leave.certify', $leave)" variant="success" icon="fas fa-certificate" size="sm">Certify</x-admin.action-button>
                            @elseif($leave->status === 'pending_recommendation')
                                <span class="inline-flex items-center px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded-md">
                                    <i class="fas fa-hourglass-half mr-1"></i> Awaiting Supervisor
                                </span>
                            @elseif($leave->status === 'pending_president_approval')
                                <span class="inline-flex items-center px-2 py-1 text-xs text-gray-500 bg-gray-100 rounded-md">
                                    <i class="fas fa-crown mr-1"></i> Awaiting President
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <x-admin.empty-state
                            icon="fas fa-inbox"
                            title="No Leave Applications Found"
                            message="There are no leave applications matching your filters."
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        @if(isset($leaves) && method_exists($leaves, 'hasPages') && $leaves->hasPages())
        <x-slot name="footer">{{ $leaves->links() }}</x-slot>
        @endif
    </x-admin.card>
</div>
@endsection
