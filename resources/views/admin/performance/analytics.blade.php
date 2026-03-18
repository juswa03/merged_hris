@extends('admin.layouts.app')

@section('title', 'Performance Analytics')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="Performance Analytics"
        description="Comprehensive performance review insights and statistics"
    />

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-admin.gradient-stat-card
            title="Total Reviews"
            :value="$totalReviews"
            icon="fas fa-file-alt"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Completed"
            :value="$completedReviews"
            icon="fas fa-check-circle"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Pending"
            :value="$pendingReviews"
            icon="fas fa-hourglass-half"
            gradientFrom="orange-500"
            gradientTo="orange-600"
        />
        <x-admin.gradient-stat-card
            title="Active Goals"
            :value="$activeGoals"
            icon="fas fa-bullseye"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-admin.card title="Reviews by Status">
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                    <i class="fas fa-chart-pie text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500">Chart visualization will appear here</p>
                    <p class="text-gray-400 text-sm mt-1">No data available yet</p>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card title="Rating Distribution">
            <div class="space-y-4">
                @forelse($ratingDistribution as $rating)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">
                                @php
                                    $ratingValue = $rating['overall_rating'] ?? $rating['rating'] ?? 0;
                                    if ($ratingValue >= 4.5) {
                                        $label = 'Outstanding (5)';
                                    } elseif ($ratingValue >= 3.5) {
                                        $label = 'Exceeds Expectations (4)';
                                    } elseif ($ratingValue >= 2.5) {
                                        $label = 'Meets Expectations (3)';
                                    } elseif ($ratingValue >= 1.5) {
                                        $label = 'Needs Improvement (2)';
                                    } else {
                                        $label = 'Unsatisfactory (1)';
                                    }
                                    echo $label;
                                @endphp
                            </span>
                            <span class="text-sm text-gray-600">{{ $rating['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full"
                                @php
                                    $ratingValue = $rating['overall_rating'] ?? $rating['rating'] ?? 0;
                                    if ($ratingValue >= 4.5) {
                                        $color = '#10b981';
                                    } elseif ($ratingValue >= 3.5) {
                                        $color = '#3b82f6';
                                    } elseif ($ratingValue >= 2.5) {
                                        $color = '#eab308';
                                    } elseif ($ratingValue >= 1.5) {
                                        $color = '#f97316';
                                    } else {
                                        $color = '#ef4444';
                                    }
                                @endphp
                                style="width: {{ $rating['percentage'] }}%; background-color: {{ $color }};"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No rating data available</p>
                    </div>
                @endforelse
            </div>
        </x-admin.card>
    </div>

    <!-- Department Comparison -->
    <x-admin.card title="Department Performance" :padding="false" class="mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Department</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Total Reviews</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Completed</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Pending</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Avg Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($departmentPerformance as $dept)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $dept['name'] }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $dept['total_reviews'] }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">{{ $dept['completed'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">{{ $dept['pending'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                <span class="text-gray-800">{{ $dept['avg_rating'] }}</span>
                                <span class="text-gray-500">/5</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-600">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>

    <!-- Recent Reviews -->
    <x-admin.card title="Recent Reviews" class="mb-6">
        <x-slot name="headerActions">
            <a href="{{ route('admin.performance.reviews.index') }}" class="text-blue-600 text-sm font-medium hover:text-blue-800">View All →</a>
        </x-slot>
        @if($recentReviews->count() > 0)
            <div class="space-y-4">
                @foreach($recentReviews as $review)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $review->employee->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $review->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">
                                    <span class="text-yellow-500">@for($i = 0; $i < $review->rating ?? 0; $i++)★@endfor</span>
                                </p>
                                <p class="text-xs text-gray-600 mt-1">{{ ucfirst($review->status) }}</p>
                            </div>
                            <a href="{{ route('admin.performance.reviews.show', $review->id) }}" class="px-3 py-1 text-blue-600 hover:text-blue-800 text-sm font-medium">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-admin.empty-state icon="fas fa-inbox" title="No reviews yet" description="Create performance reviews to get started" />
        @endif
    </x-admin.card>

    <!-- Export Section -->
    <x-admin.card title="Export Analytics" subtitle="Download detailed performance reports and statistics">
        <x-slot name="headerActions">
            <div class="flex space-x-3">
                <x-admin.action-button variant="danger" icon="fas fa-file-pdf" href="{{ route('admin.performance.analytics') }}?export=pdf">
                    PDF Report
                </x-admin.action-button>
                <x-admin.action-button variant="success" icon="fas fa-file-csv" href="{{ route('admin.performance.analytics') }}?export=csv">
                    CSV Export
                </x-admin.action-button>
            </div>
        </x-slot>
    </x-admin.card>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any chart initialization or data loading here
        console.log('Performance Analytics loaded');
    });
</script>
@endpush
@endsection
