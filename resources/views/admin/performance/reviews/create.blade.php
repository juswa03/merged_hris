@extends('layouts.app')

@section('title', 'Create Performance Review')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('performance.reviews.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Performance Review</h1>
                <p class="text-sm text-gray-600 mt-1">Set up a new employee performance evaluation</p>
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
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Review Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('performance.reviews.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Employee Selection -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Employee <span class="text-red-500">*</span>
                    </label>
                    <select name="employee_id" id="employee_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('employee_id') border-red-500 @enderror">
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} - {{ $employee->department->name ?? 'No Dept' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Type -->
                <div>
                    <label for="review_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Review Type <span class="text-red-500">*</span>
                    </label>
                    <select name="review_type" id="review_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('review_type') border-red-500 @enderror">
                        <option value="">-- Select Review Type --</option>
                        <option value="annual" {{ old('review_type') === 'annual' ? 'selected' : '' }}>Annual Review</option>
                        <option value="mid_year" {{ old('review_type') === 'mid_year' ? 'selected' : '' }}>Mid-Year Review</option>
                        <option value="quarterly" {{ old('review_type') === 'quarterly' ? 'selected' : '' }}>Quarterly Review</option>
                        <option value="probation" {{ old('review_type') === 'probation' ? 'selected' : '' }}>Probation Review</option>
                    </select>
                    @error('review_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Period -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="review_period_start" class="block text-sm font-medium text-gray-700 mb-2">
                            Review Period Start <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="review_period_start" id="review_period_start" required
                               value="{{ old('review_period_start') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('review_period_start') border-red-500 @enderror">
                        @error('review_period_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="review_period_end" class="block text-sm font-medium text-gray-700 mb-2">
                            Review Period End <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="review_period_end" id="review_period_end" required
                               value="{{ old('review_period_end') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('review_period_end') border-red-500 @enderror">
                        @error('review_period_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                After creating this review, you will be directed to the evaluation form where you can rate the employee on various performance criteria.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('performance.reviews.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        <i class="fas fa-arrow-right mr-2"></i> Create & Continue to Evaluation
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Criteria Preview -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Active Performance Criteria</h3>
        <p class="text-sm text-gray-600 mb-4">The following criteria will be used for this evaluation:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($criteria as $criterion)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">{{ $criterion->name }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ $criterion->description }}</p>
                    </div>
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $criterion->weight }}%
                    </span>
                </div>
                <div class="mt-2">
                    <span class="text-xs text-gray-500">
                        <i class="fas fa-tag mr-1"></i> {{ ucfirst($criterion->category) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        @if($criteria->isEmpty())
        <div class="text-center py-8">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-3"></i>
            <p class="text-gray-600">No active performance criteria found. Please add criteria before creating reviews.</p>
            <a href="{{ route('performance.criteria.create') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800">
                <i class="fas fa-plus mr-1"></i> Add Performance Criteria
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
