@extends('layouts.app')

@section('title', 'Goal Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('performance.goals.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Goal Details</h1>
                    <p class="text-sm text-gray-600 mt-1">View performance goal information</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('performance.goals.edit', $goal->id) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <button onclick="deleteGoal({{ $goal->id }})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Goal Header Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">{{ $goal->title }}</h2>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span>
                        <i class="fas fa-user mr-1"></i> {{ $goal->employee->full_name }}
                    </span>
                    <span>
                        <i class="fas fa-building mr-1"></i> {{ $goal->employee->department->name ?? 'No Department' }}
                    </span>
                    <span>
                        <i class="fas fa-user-tie mr-1"></i> Set by {{ $goal->setter->email ?? 'N/A' }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    {{ $goal->priority === 'high' ? 'bg-red-100 text-red-800' :
                       ($goal->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                    <i class="fas fa-flag mr-1"></i> {{ ucfirst($goal->priority) }} Priority
                </span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    {{ $goal->status === 'completed' ? 'bg-green-100 text-green-800' :
                       ($goal->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                       ($goal->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                </span>
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                    <i class="fas fa-layer-group mr-1"></i> {{ ucfirst($goal->category) }}
                </span>
            </div>
        </div>

        @if($goal->description)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
            <p class="text-gray-700 whitespace-pre-line">{{ $goal->description }}</p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Progress Section -->
        <div class="lg:col-span-2">
            <!-- Progress Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress Tracking</h3>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-700">Current Progress</span>
                        <span class="text-3xl font-bold text-blue-600">{{ $goal->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6">
                        <div class="h-6 rounded-full transition-all flex items-center justify-end pr-2
                            {{ $goal->progress_percentage >= 75 ? 'bg-green-500' :
                               ($goal->progress_percentage >= 50 ? 'bg-blue-500' :
                               ($goal->progress_percentage >= 25 ? 'bg-yellow-500' : 'bg-red-500')) }}"
                             style="width: {{ $goal->progress_percentage }}%">
                            @if($goal->progress_percentage >= 15)
                            <span class="text-xs font-semibold text-white">{{ $goal->progress_percentage }}%</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Milestone Indicators -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center
                            {{ $goal->progress_percentage >= 25 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $goal->progress_percentage >= 25 ? 'fa-check' : 'fa-circle' }}"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-600 mt-2">25%</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center
                            {{ $goal->progress_percentage >= 50 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $goal->progress_percentage >= 50 ? 'fa-check' : 'fa-circle' }}"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-600 mt-2">50%</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center
                            {{ $goal->progress_percentage >= 75 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $goal->progress_percentage >= 75 ? 'fa-check' : 'fa-circle' }}"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-600 mt-2">75%</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center
                            {{ $goal->progress_percentage >= 100 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $goal->progress_percentage >= 100 ? 'fa-trophy' : 'fa-circle' }}"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-600 mt-2">100%</p>
                    </div>
                </div>

                <!-- Progress Notes -->
                @if($goal->notes)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-sticky-note mr-2"></i> Progress Notes
                    </h4>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $goal->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Timeline Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-flag-checkered text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Created</h4>
                            <p class="text-sm text-gray-600">{{ $goal->created_at->format('M d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ $goal->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Target Date</h4>
                            <p class="text-sm text-gray-600">{{ $goal->target_date->format('M d, Y') }}</p>
                            <p class="text-xs {{ $goal->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                {{ $goal->isOverdue() ? 'OVERDUE' : $goal->target_date->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    @if($goal->completion_date)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Completed</h4>
                            <p class="text-sm text-gray-600">{{ $goal->completion_date->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $goal->completion_date->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Last Updated</h4>
                            <p class="text-sm text-gray-600">{{ $goal->updated_at->format('M d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ $goal->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Employee Info Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $goal->employee->full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $goal->employee->position ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $goal->employee->department->name ?? 'No Dept' }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('performance.goals.edit', $goal->id) }}"
                       class="block w-full px-4 py-2 text-center bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        <i class="fas fa-edit mr-2"></i> Update Progress
                    </a>
                    <a href="{{ route('performance.goals.index', ['employee_id' => $goal->employee_id]) }}"
                       class="block w-full px-4 py-2 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                        <i class="fas fa-list mr-2"></i> View All Employee Goals
                    </a>
                </div>
            </div>

            <!-- Status Alert -->
            @if($goal->isOverdue())
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Goal Overdue</h3>
                        <p class="mt-1 text-sm text-red-700">
                            This goal is {{ $goal->target_date->diffForHumans() }} its target date.
                        </p>
                    </div>
                </div>
            </div>
            @elseif($goal->status === 'completed')
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Goal Completed!</h3>
                        <p class="mt-1 text-sm text-green-700">
                            Congratulations on achieving this goal!
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
function deleteGoal() {
    if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
        fetch(`/performance/goals/{{ $goal->id }}`, {
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
                window.location.href = '{{ route("performance.goals.index") }}';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the goal');
        });
    }
}
</script>
@endpush
@endsection
