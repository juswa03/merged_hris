@extends('admin.layouts.app')

@section('title', 'Performance Criteria')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="Performance Criteria"
        description="Manage evaluation criteria for performance reviews"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.performance.criteria.create') }}" icon="fas fa-plus">
                New Criterion
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-admin.gradient-stat-card
            title="Total Criteria"
            :value="$stats['total_criteria']"
            icon="fas fa-list-check"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Active Criteria"
            :value="$stats['active_criteria']"
            icon="fas fa-check-circle"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Inactive Criteria"
            :value="$stats['inactive_criteria']"
            icon="fas fa-pause-circle"
            gradientFrom="red-500"
            gradientTo="red-600"
        />
        <x-admin.gradient-stat-card
            title="Total Weight"
            :value="$stats['total_weight'] . '%'"
            icon="fas fa-weight-hanging"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
    </div>

    <!-- Weight Alert -->
    @if($stats['total_weight'] !== 100)
    <x-admin.alert type="warning" class="mb-6">
        <strong>Weight Imbalance:</strong>
        The total weight of active criteria is {{ $stats['total_weight'] }}%. For accurate ratings, it should equal 100%.
    </x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.performance.criteria.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                       placeholder="Search by name or description...">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Categories</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="behavioral" {{ request('category') === 'behavioral' ? 'selected' : '' }}>Behavioral</option>
                    <option value="leadership" {{ request('category') === 'leadership' ? 'selected' : '' }}>Leadership</option>
                    <option value="communication" {{ request('category') === 'communication' ? 'selected' : '' }}>Communication</option>
                    <option value="productivity" {{ request('category') === 'productivity' ? 'selected' : '' }}>Productivity</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter" class="w-full justify-center">
                    Filter
                </x-admin.action-button>
            </div>
        </form>
    </x-admin.card>

    <!-- Criteria Grid by Category -->
    @php
        $groupedCriteria = $criteria->groupBy('category');
    @endphp

    @foreach(['technical', 'productivity', 'behavioral', 'communication', 'leadership'] as $category)
        @if(isset($groupedCriteria[$category]) && $groupedCriteria[$category]->isNotEmpty())
        <x-admin.card :title="ucfirst($category) . ' Criteria'" class="mb-6">
            <x-slot name="title">
                <span class="flex items-center">
                    <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                    {{ ucfirst($category) }} Criteria
                </span>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($groupedCriteria[$category] as $criterion)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-900">{{ $criterion->name }}</h4>
                            <p class="text-xs text-gray-600 mt-1">{{ $criterion->description }}</p>
                        </div>
                        <div class="ml-3 flex flex-col items-end gap-2">
                            <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $criterion->weight }}%
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $criterion->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $criterion->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    @if($criterion->ratings_count > 0)
                    <div class="text-xs text-gray-500 mb-3">
                        <i class="fas fa-chart-bar mr-1"></i> Used in {{ $criterion->ratings_count }} {{ Str::plural('review', $criterion->ratings_count) }}
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                        <button onclick="toggleStatus({{ $criterion->id }})"
                                class="px-3 py-1 text-xs {{ $criterion->is_active ? 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' : 'text-green-600 hover:text-green-800 hover:bg-green-50' }} rounded">
                            <i class="fas {{ $criterion->is_active ? 'fa-pause' : 'fa-play' }} mr-1"></i>
                            {{ $criterion->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <a href="{{ route('admin.performance.criteria.edit', $criterion->id) }}"
                           class="px-3 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <button onclick="deleteCriterion({{ $criterion->id }})"
                                class="px-3 py-1 text-xs text-red-600 hover:text-red-800 hover:bg-red-50 rounded">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </x-admin.card>
        @endif
    @endforeach

    @if($criteria->isEmpty())
    <x-admin.empty-state
        icon="fas fa-list-check"
        title="No performance criteria found"
        description="Create criteria to evaluate employee performance"
    >
        <x-slot name="action">
            <x-admin.action-button href="{{ route('admin.performance.criteria.create') }}" icon="fas fa-plus">
                Create First Criterion
            </x-admin.action-button>
        </x-slot>
    </x-admin.empty-state>
    @endif

    @if($criteria->hasPages())
    <div class="mt-6">
        {{ $criteria->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function toggleStatus(id) {
    fetch(`/performance/criteria/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status');
    });
}

function deleteCriterion(id) {
    if (confirm('Are you sure you want to delete this criterion? This action cannot be undone.')) {
        fetch(`/performance/criteria/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the criterion');
        });
    }
}
</script>
@endpush
@endsection
