@extends('layouts.app')

@section('title', 'Performance Criteria')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Performance Criteria</h1>
            <p class="text-sm text-gray-600 mt-1">Manage evaluation criteria for performance reviews</p>
        </div>
        <a href="{{ route('performance.criteria.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
            <i class="fas fa-plus mr-2"></i> New Criterion
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Criteria</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_criteria'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-list-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Criteria</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_criteria'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Inactive Criteria</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ $stats['inactive_criteria'] }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <i class="fas fa-pause-circle text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Weight</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['total_weight'] }}%</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-weight-hanging text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Weight Alert -->
    @if($stats['total_weight'] !== 100)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Weight Imbalance</h3>
                <p class="mt-1 text-sm text-yellow-700">
                    The total weight of active criteria is {{ $stats['total_weight'] }}%. For accurate ratings, it should equal 100%.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('performance.criteria.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Search by name or description...">
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <option value="">All Categories</option>
                    <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="behavioral" {{ request('category') === 'behavioral' ? 'selected' : '' }}>Behavioral</option>
                    <option value="leadership" {{ request('category') === 'leadership' ? 'selected' : '' }}>Leadership</option>
                    <option value="communication" {{ request('category') === 'communication' ? 'selected' : '' }}>Communication</option>
                    <option value="productivity" {{ request('category') === 'productivity' ? 'selected' : '' }}>Productivity</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Criteria Grid by Category -->
    @php
        $groupedCriteria = $criteria->groupBy('category');
    @endphp

    @foreach(['technical', 'productivity', 'behavioral', 'communication', 'leadership'] as $category)
        @if(isset($groupedCriteria[$category]) && $groupedCriteria[$category]->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 capitalize flex items-center">
                <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                {{ ucfirst($category) }} Criteria
            </h3>

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
                        <a href="{{ route('performance.criteria.edit', $criterion->id) }}"
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
        </div>
        @endif
    @endforeach

    @if($criteria->isEmpty())
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-list-check text-gray-400 text-6xl mb-4"></i>
        <p class="text-gray-500 text-lg">No performance criteria found</p>
        <p class="text-gray-400 text-sm mt-2">Create criteria to evaluate employee performance</p>
        <a href="{{ route('performance.criteria.create') }}" class="mt-4 inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
            <i class="fas fa-plus mr-2"></i> Create First Criterion
        </a>
    </div>
    @endif

    <!-- Pagination -->
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
