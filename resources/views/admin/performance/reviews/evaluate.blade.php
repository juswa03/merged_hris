@extends('layouts.app')

@section('title', 'Evaluate Performance')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('performance.reviews.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Performance Evaluation</h1>
                <p class="text-sm text-gray-600 mt-1">Complete the performance review for {{ $review->employee->full_name }}</p>
            </div>
        </div>
    </div>

    <!-- Employee Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center gap-6">
            <div class="flex-shrink-0">
                <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 text-3xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-semibold text-gray-900">{{ $review->employee->full_name }}</h2>
                <p class="text-gray-600">{{ $review->employee->position ?? 'N/A' }} - {{ $review->employee->department->name ?? 'No Dept' }}</p>
                <div class="mt-2 flex gap-4">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i> Review Period: {{ $review->review_period_start->format('M d, Y') }} - {{ $review->review_period_end->format('M d, Y') }}
                    </span>
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-tag mr-1"></i> {{ ucfirst(str_replace('_', ' ', $review->review_type)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Evaluation Form -->
    <form action="{{ route('performance.reviews.storeEvaluation', $review->id) }}" method="POST">
        @csrf

        <!-- Performance Criteria Ratings -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Criteria Ratings</h3>
            <p class="text-sm text-gray-600 mb-6">Rate the employee on each criterion using a scale of 1-5:</p>

            <!-- Rating Scale Legend -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-5 gap-2 text-center text-xs">
                    <div>
                        <div class="font-semibold text-red-600">1 - Unsatisfactory</div>
                        <div class="text-gray-600">Significant improvement needed</div>
                    </div>
                    <div>
                        <div class="font-semibold text-orange-600">2 - Needs Improvement</div>
                        <div class="text-gray-600">Below expectations</div>
                    </div>
                    <div>
                        <div class="font-semibold text-yellow-600">3 - Meets Expectations</div>
                        <div class="text-gray-600">Satisfactory performance</div>
                    </div>
                    <div>
                        <div class="font-semibold text-blue-600">4 - Exceeds Expectations</div>
                        <div class="text-gray-600">Above average</div>
                    </div>
                    <div>
                        <div class="font-semibold text-green-600">5 - Outstanding</div>
                        <div class="text-gray-600">Exceptional performance</div>
                    </div>
                </div>
            </div>

            <!-- Criteria by Category -->
            @php
                $groupedRatings = $review->ratings->groupBy(function($rating) {
                    return $rating->criteria->category;
                });
            @endphp

            @foreach($groupedRatings as $category => $ratings)
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-3 capitalize">
                    <i class="fas fa-folder-open mr-2"></i> {{ ucfirst($category) }}
                </h4>

                <div class="space-y-4">
                    @foreach($ratings as $rating)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <label class="text-sm font-medium text-gray-900">
                                    {{ $rating->criteria->name }}
                                    <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-600 mt-1">{{ $rating->criteria->description }}</p>
                            </div>
                            <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Weight: {{ $rating->criteria->weight }}%
                            </span>
                        </div>

                        <!-- Rating Stars -->
                        <div class="flex items-center gap-2 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="ratings[{{ $rating->criteria->id }}][rating]"
                                       value="{{ $i }}"
                                       {{ old("ratings.{$rating->criteria->id}.rating", $rating->rating) == $i ? 'checked' : '' }}
                                       class="hidden rating-input"
                                       data-criterion-id="{{ $rating->criteria->id }}">
                                <div class="rating-star w-12 h-12 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition-colors
                                    {{ old("ratings.{$rating->criteria->id}.rating", $rating->rating) == $i ? 'bg-blue-100 border-blue-500' : '' }}">
                                    <span class="text-xl font-bold {{ old("ratings.{$rating->criteria->id}.rating", $rating->rating) == $i ? 'text-blue-600' : 'text-gray-400' }}">
                                        {{ $i }}
                                    </span>
                                </div>
                            </label>
                            @endfor
                        </div>

                        <!-- Comments -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Comments (Optional)</label>
                            <textarea name="ratings[{{ $rating->criteria->id }}][comments]"
                                      rows="2"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Add specific observations or examples...">{{ old("ratings.{$rating->criteria->id}.comments", $rating->comments) }}</textarea>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Overall Assessment -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Assessment</h3>

            <div class="space-y-6">
                <!-- Strengths -->
                <div>
                    <label for="strengths" class="block text-sm font-medium text-gray-700 mb-2">
                        Key Strengths
                    </label>
                    <textarea name="strengths" id="strengths" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe the employee's notable strengths and positive contributions...">{{ old('strengths', $review->strengths) }}</textarea>
                </div>

                <!-- Areas for Improvement -->
                <div>
                    <label for="areas_for_improvement" class="block text-sm font-medium text-gray-700 mb-2">
                        Areas for Improvement
                    </label>
                    <textarea name="areas_for_improvement" id="areas_for_improvement" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Identify areas where the employee can improve...">{{ old('areas_for_improvement', $review->areas_for_improvement) }}</textarea>
                </div>

                <!-- Recommendations -->
                <div>
                    <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                        Recommendations & Development Plan
                    </label>
                    <textarea name="recommendations" id="recommendations" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Provide recommendations for professional development and growth...">{{ old('recommendations', $review->recommendations) }}</textarea>
                </div>

                <!-- Reviewer Comments -->
                <div>
                    <label for="reviewer_comments" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Reviewer Comments
                    </label>
                    <textarea name="reviewer_comments" id="reviewer_comments" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Any additional comments or notes about this review...">{{ old('reviewer_comments', $review->reviewer_comments) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between bg-white rounded-lg shadow-md p-6">
            <a href="{{ route('performance.reviews.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                <i class="fas fa-times mr-2"></i> Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                <i class="fas fa-check mr-2"></i> Complete Review
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle rating star clicks
    const ratingInputs = document.querySelectorAll('.rating-input');

    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const criterionId = this.getAttribute('data-criterion-id');
            const allStars = document.querySelectorAll(`[data-criterion-id="${criterionId}"]`);

            allStars.forEach(star => {
                const parent = star.parentElement.querySelector('.rating-star');
                const label = parent.querySelector('span');

                if (star.checked) {
                    parent.classList.add('bg-blue-100', 'border-blue-500');
                    parent.classList.remove('border-gray-300');
                    label.classList.add('text-blue-600');
                    label.classList.remove('text-gray-400');
                } else {
                    parent.classList.remove('bg-blue-100', 'border-blue-500');
                    parent.classList.add('border-gray-300');
                    label.classList.remove('text-blue-600');
                    label.classList.add('text-gray-400');
                }
            });
        });
    });
});
</script>
@endpush
@endsection
