@extends('layouts.app')

@section('title', 'Create Position')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('positions.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Position</h1>
                <p class="text-sm text-gray-600 mt-1">Add a new position to your organization</p>
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

    <!-- Form -->
    <form action="{{ route('positions.store') }}" method="POST" class="bg-white rounded-lg shadow-md">
        @csrf

        <div class="p-6 space-y-6">
            <!-- Basic Information Section -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Position Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Position Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., Software Engineer"
                        >
                        <p class="mt-1 text-xs text-gray-500">Official position name (must be unique)</p>
                    </div>

                    <!-- Position Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Position Title
                        </label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            value="{{ old('title') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., Senior Software Engineer"
                        >
                        <p class="mt-1 text-xs text-gray-500">Alternative or display title (optional)</p>
                    </div>

                    <!-- Level -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                            Position Level
                        </label>
                        <select
                            name="level"
                            id="level"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">-- Select Level --</option>
                            <option value="Entry Level" {{ old('level') == 'Entry Level' ? 'selected' : '' }}>Entry Level</option>
                            <option value="Mid Level" {{ old('level') == 'Mid Level' ? 'selected' : '' }}>Mid Level</option>
                            <option value="Senior Level" {{ old('level') == 'Senior Level' ? 'selected' : '' }}>Senior Level</option>
                            <option value="Managerial" {{ old('level') == 'Managerial' ? 'selected' : '' }}>Managerial</option>
                            <option value="Executive" {{ old('level') == 'Executive' ? 'selected' : '' }}>Executive</option>
                        </select>
                    </div>

                    <!-- Salary Grade -->
                    <div>
                        <label for="salary_grade" class="block text-sm font-medium text-gray-700 mb-2">
                            Salary Grade
                        </label>
                        <input
                            type="number"
                            name="salary_grade"
                            id="salary_grade"
                            value="{{ old('salary_grade') }}"
                            min="1"
                            max="30"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="1-30"
                        >
                        <p class="mt-1 text-xs text-gray-500">Government salary grade (1-30)</p>
                    </div>
                </div>
            </div>

            <!-- Salary Range Section -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Salary Range</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Minimum Salary -->
                    <div>
                        <label for="min_salary" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Salary (₱)
                        </label>
                        <input
                            type="number"
                            name="min_salary"
                            id="min_salary"
                            value="{{ old('min_salary') }}"
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0.00"
                        >
                    </div>

                    <!-- Maximum Salary -->
                    <div>
                        <label for="max_salary" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Salary (₱)
                        </label>
                        <input
                            type="number"
                            name="max_salary"
                            id="max_salary"
                            value="{{ old('max_salary') }}"
                            min="0"
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0.00"
                        >
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Details</h2>
                <div class="space-y-6">
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Job Description
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Describe the responsibilities and duties of this position..."
                        >{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Brief overview of the position's responsibilities</p>
                    </div>

                    <!-- Requirements -->
                    <div>
                        <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">
                            Qualifications & Requirements
                        </label>
                        <textarea
                            name="requirements"
                            id="requirements"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="List the required qualifications, skills, and experience..."
                        >{{ old('requirements') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Education, experience, skills, and certifications required</p>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Status</h2>
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Position
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500 ml-6">Inactive positions cannot be assigned to new employees</p>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <a href="{{ route('positions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i> Create Position
            </button>
        </div>
    </form>
</div>
@endsection
