@extends('admin.layouts.app')

@section('title', 'Performance Review Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.performance.reviews.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Performance Review Details</h1>
                    <p class="text-sm text-gray-600 mt-1">Review for {{ $review->employee->full_name }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                @if($review->status === 'draft')
                <a href="{{ route('admin.performance.reviews.evaluate', $review->id) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md">
                    <i class="fas fa-edit mr-2"></i> Continue Evaluation
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Employee & Review Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Employee Info -->
            <div class="lg:col-span-2">
                <div class="flex items-start gap-6">
                    <div class="flex-shrink-0">
                        <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-4xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $review->employee->full_name }}</h2>
                        <p class="text-gray-600 mt-1">{{ $review->employee->position ?? 'N/A' }}</p>
                        <p class="text-gray-500 text-sm">{{ $review->employee->department->name ?? 'No Department' }}</p>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-600">Review Period</label>
                                <p class="text-sm text-gray-900">{{ $review->review_period_start->format('M d, Y') }} - {{ $review->review_period_end->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Review Type</label>
                                <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $review->review_type)) }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Reviewed By</label>
                                <p class="text-sm text-gray-900">{{ $review->reviewer->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600">Completed Date</label>
                                <p class="text-sm text-gray-900">{{ $review->completed_at ? $review->completed_at->format('M d, Y') : 'Not completed' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Rating -->
            <div class="lg:col-span-1 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 text-center">
                <h3 class="text-sm font-medium text-gray-600 mb-2">Overall Rating</h3>
                @if($review->overall_rating)
                    <div class="text-5xl font-bold text-blue-600 mb-2">{{ number_format($review->overall_rating, 2) }}</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $review->rating_label }}</div>
                    <div class="mt-4">
                        <div class="flex justify-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($review->overall_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </div>
                @else
                    <div class="text-3xl text-gray-400 mb-2">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="text-sm text-gray-600">Not yet rated</div>
                @endif

                <!-- Status Badge -->
                <div class="mt-4">
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                        {{ $review->status === 'approved' ? 'bg-green-100 text-green-800' :
                           ($review->status === 'completed' ? 'bg-blue-100 text-blue-800' :
                           ($review->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($review->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Criteria Ratings -->
    @if($review->ratings->isNotEmpty())
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Criteria Ratings</h3>

        @php
            $groupedRatings = $review->ratings->groupBy(function($rating) {
                return $rating->criteria->category;
            });
        @endphp

        @foreach($groupedRatings as $category => $ratings)
        <div class="mb-6 last:mb-0">
            <h4 class="text-md font-semibold text-gray-800 mb-3 capitalize flex items-center">
                <i class="fas fa-folder-open mr-2 text-blue-600"></i> {{ ucfirst($category) }}
            </h4>

            <div class="space-y-3">
                @foreach($ratings as $rating)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h5 class="text-sm font-medium text-gray-900">{{ $rating->criteria->name }}</h5>
                            <p class="text-xs text-gray-600 mt-1">{{ $rating->criteria->description }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            <div class="text-2xl font-bold
                                {{ $rating->rating >= 4 ? 'text-green-600' :
                                   ($rating->rating == 3 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $rating->rating }}/5
                            </div>
                            <div class="text-xs text-gray-500">Weight: {{ $rating->criteria->weight }}%</div>
                        </div>
                    </div>

                    @if($rating->comments)
                    <div class="mt-3 bg-gray-50 rounded p-3">
                        <p class="text-xs font-medium text-gray-700 mb-1">Comments:</p>
                        <p class="text-sm text-gray-600">{{ $rating->comments }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Overall Assessment -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Strengths -->
        @if($review->strengths)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-green-700 mb-3 flex items-center">
                <i class="fas fa-thumbs-up mr-2"></i> Key Strengths
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->strengths }}</p>
        </div>
        @endif

        <!-- Areas for Improvement -->
        @if($review->areas_for_improvement)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-orange-700 mb-3 flex items-center">
                <i class="fas fa-chart-line mr-2"></i> Areas for Improvement
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->areas_for_improvement }}</p>
        </div>
        @endif
    </div>

    <!-- Recommendations -->
    @if($review->recommendations)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i> Recommendations & Development Plan
        </h3>
        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->recommendations }}</p>
    </div>
    @endif

    <!-- Comments Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Reviewer Comments -->
        @if($review->reviewer_comments)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-comment mr-2"></i> Reviewer Comments
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->reviewer_comments }}</p>
        </div>
        @endif

        <!-- Employee Comments -->
        @if($review->employee_comments)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-user-comment mr-2"></i> Employee Comments
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->employee_comments }}</p>
        </div>
        @endif

        <!-- HR Comments -->
        @if($review->hr_comments)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                <i class="fas fa-briefcase mr-2"></i> HR Comments
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->hr_comments }}</p>
        </div>
        @endif
    </div>

    <!-- Approval Information -->
    @if($review->status === 'approved' && $review->approver)
    <div class="mt-6 bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">
                    This review was approved by <strong>{{ $review->approver->email }}</strong> on {{ $review->approved_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
