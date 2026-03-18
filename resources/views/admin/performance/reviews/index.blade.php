@extends('admin.layouts.app')

@section('title', 'Performance Reviews')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="Performance Reviews"
        description="Manage and track employee performance evaluations"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.performance.reviews.create') }}" icon="fas fa-plus">
                New Review
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-admin.gradient-stat-card
            title="Total Reviews"
            :value="$stats['total_reviews']"
            icon="fas fa-clipboard-list"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Pending Reviews"
            :value="$stats['pending_reviews']"
            icon="fas fa-clock"
            gradientFrom="yellow-500"
            gradientTo="yellow-600"
        />
        <x-admin.gradient-stat-card
            title="Completed Reviews"
            :value="$stats['completed_reviews']"
            icon="fas fa-check-circle"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Average Rating"
            :value="number_format($stats['avg_rating'] ?? 0, 2)"
            icon="fas fa-star"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.performance.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Employee</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                       placeholder="Search by employee name...">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div>
                <label for="review_type" class="block text-sm font-medium text-gray-700 mb-1">Review Type</label>
                <select name="review_type" id="review_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="annual" {{ request('review_type') === 'annual' ? 'selected' : '' }}>Annual</option>
                    <option value="mid_year" {{ request('review_type') === 'mid_year' ? 'selected' : '' }}>Mid-Year</option>
                    <option value="quarterly" {{ request('review_type') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="probation" {{ request('review_type') === 'probation' ? 'selected' : '' }}>Probation</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter" class="w-full justify-center">
                    Filter
                </x-admin.action-button>
            </div>
        </form>
    </x-admin.card>

    <!-- Reviews Table -->
    <x-admin.card :padding="false">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employee
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Review Period
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rating
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Reviewer
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $review->employee->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $review->employee->department->name ?? 'No Dept' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $review->review_period_start->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">to {{ $review->review_period_end->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $review->review_type)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($review->overall_rating)
                            <div class="text-lg font-bold text-yellow-600">{{ number_format($review->overall_rating, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ $review->rating_label }}</div>
                        @else
                            <span class="text-gray-400">Not rated</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $review->status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($review->status === 'completed' ? 'bg-blue-100 text-blue-800' :
                               ($review->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($review->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                        {{ $review->reviewer->email ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="{{ route('admin.performance.reviews.show', $review->id) }}"
                           class="text-blue-600 hover:text-blue-900 mr-3" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($review->status === 'draft')
                        <a href="{{ route('admin.performance.reviews.evaluate', $review->id) }}"
                           class="text-green-600 hover:text-green-900 mr-3" title="Evaluate">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteReview({{ $review->id }})"
                                class="text-red-600 hover:text-red-900" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <x-admin.empty-state
                            icon="fas fa-clipboard-list"
                            title="No performance reviews found"
                            description="Create a new review to get started"
                        />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($reviews->hasPages())
        <x-slot name="footer">
            {{ $reviews->links() }}
        </x-slot>
        @endif
    </x-admin.card>
</div>

@push('scripts')
<script>
function deleteReview(id) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        fetch(`/performance/reviews/${id}`, {
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
            alert('An error occurred while deleting the review');
        });
    }
}
</script>
@endpush
@endsection
