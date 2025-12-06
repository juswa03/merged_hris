@extends('layouts.app')

@section('title', 'Edit Performance Goal')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('performance.goals.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Performance Goal</h1>
                <p class="text-sm text-gray-600 mt-1">Update goal details and track progress</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('performance.goals.update', $goal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Employee Selection -->
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Employee <span class="text-red-500">*</span>
                            </label>
                            <select name="employee_id" id="employee_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $goal->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} - {{ $employee->department->name ?? 'No Dept' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Goal Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Goal Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                   value="{{ old('title', $goal->title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Goal Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('description', $goal->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select name="category" id="category" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md">
                                    <option value="individual" {{ old('category', $goal->category) === 'individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="team" {{ old('category', $goal->category) === 'team' ? 'selected' : '' }}>Team</option>
                                    <option value="department" {{ old('category', $goal->category) === 'department' ? 'selected' : '' }}>Department</option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                    Priority <span class="text-red-500">*</span>
                                </label>
                                <select name="priority" id="priority" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md">
                                    <option value="low" {{ old('priority', $goal->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $goal->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $goal->priority) === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md">
                                    <option value="not_started" {{ old('status', $goal->status) === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                    <option value="in_progress" {{ old('status', $goal->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $goal->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $goal->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <!-- Progress Percentage -->
                        <div>
                            <label for="progress_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                                Progress <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-4">
                                <input type="range" name="progress_percentage" id="progress_percentage"
                                       min="0" max="100" step="5"
                                       value="{{ old('progress_percentage', $goal->progress_percentage) }}"
                                       class="flex-1"
                                       oninput="document.getElementById('progress_value').textContent = this.value">
                                <span id="progress_value" class="text-2xl font-bold text-blue-600 w-16 text-right">
                                    {{ old('progress_percentage', $goal->progress_percentage) }}
                                </span>
                                <span class="text-gray-600">%</span>
                            </div>
                            <div class="mt-3 w-full bg-gray-200 rounded-full h-3">
                                <div id="progress_bar" class="h-3 rounded-full bg-blue-600 transition-all"
                                     style="width: {{ old('progress_percentage', $goal->progress_percentage) }}%"></div>
                            </div>
                        </div>

                        <!-- Target Date -->
                        <div>
                            <label for="target_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="target_date" id="target_date" required
                                   value="{{ old('target_date', $goal->target_date->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Progress Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Add notes about progress, obstacles, or achievements">{{ old('notes', $goal->notes) }}</textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('performance.goals.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                                <i class="fas fa-save mr-2"></i> Update Goal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Goal Information Sidebar -->
        <div class="lg:col-span-1">
            <!-- Goal Stats -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Goal Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Set By</label>
                        <p class="mt-1 text-gray-900">{{ $goal->setter->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Created Date</label>
                        <p class="mt-1 text-gray-900">{{ $goal->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $goal->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Last Updated</label>
                        <p class="mt-1 text-gray-900">{{ $goal->updated_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $goal->updated_at->diffForHumans() }}</p>
                    </div>
                    @if($goal->completion_date)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Completed Date</label>
                        <p class="mt-1 text-gray-900">{{ $goal->completion_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status Indicator -->
            @if($goal->isOverdue())
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Goal Overdue</h3>
                        <p class="mt-1 text-sm text-red-700">
                            This goal is past its target date. Consider updating the status or extending the deadline.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update progress bar when slider changes
document.getElementById('progress_percentage').addEventListener('input', function(e) {
    const value = e.target.value;
    document.getElementById('progress_value').textContent = value;
    document.getElementById('progress_bar').style.width = value + '%';

    // Change color based on progress
    const bar = document.getElementById('progress_bar');
    if (value >= 75) {
        bar.className = 'h-3 rounded-full bg-green-600 transition-all';
    } else if (value >= 50) {
        bar.className = 'h-3 rounded-full bg-blue-600 transition-all';
    } else if (value >= 25) {
        bar.className = 'h-3 rounded-full bg-yellow-500 transition-all';
    } else {
        bar.className = 'h-3 rounded-full bg-red-500 transition-all';
    }
});
</script>
@endpush
@endsection
