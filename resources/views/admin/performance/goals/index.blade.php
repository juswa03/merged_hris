@extends('layouts.app')

@section('title', 'Performance Goals')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Performance Goals</h1>
            <p class="text-sm text-gray-600 mt-1">Set and track employee performance goals</p>
        </div>
        <a href="{{ route('performance.goals.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
            <i class="fas fa-plus mr-2"></i> New Goal
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Goals</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_goals'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-bullseye text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Goals</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['active_goals'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed Goals</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed_goals'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Overdue Goals</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue_goals'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('performance.goals.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Search goals or employees...">
            </div>

            <!-- Employee Filter -->
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select name="employee_id" id="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <option value="">All Statuses</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select name="priority" id="priority" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
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

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($goals as $goal)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <!-- Goal Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $goal->title }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-user mr-1"></i> {{ $goal->employee->full_name }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-building mr-1"></i> {{ $goal->employee->department->name ?? 'No Dept' }}
                    </p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $goal->priority === 'high' ? 'bg-red-100 text-red-800' :
                           ($goal->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($goal->priority) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $goal->status === 'completed' ? 'bg-green-100 text-green-800' :
                           ($goal->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                           ($goal->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                    </span>
                </div>
            </div>

            <!-- Goal Description -->
            @if($goal->description)
            <p class="text-sm text-gray-700 mb-4 line-clamp-2">{{ $goal->description }}</p>
            @endif

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-bold text-blue-600">{{ $goal->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all
                        {{ $goal->progress_percentage >= 75 ? 'bg-green-500' :
                           ($goal->progress_percentage >= 50 ? 'bg-blue-500' :
                           ($goal->progress_percentage >= 25 ? 'bg-yellow-500' : 'bg-red-500')) }}"
                         style="width: {{ $goal->progress_percentage }}%"></div>
                </div>
            </div>

            <!-- Goal Meta Info -->
            <div class="flex items-center justify-between text-sm text-gray-600 mb-4">
                <div>
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span class="{{ $goal->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                        Target: {{ $goal->target_date->format('M d, Y') }}
                        @if($goal->isOverdue())
                            <span class="ml-1">(Overdue)</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                        {{ ucfirst($goal->category) }}
                    </span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200">
                <a href="{{ route('performance.goals.show', $goal->id) }}"
                   class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded">
                    <i class="fas fa-eye mr-1"></i> View
                </a>
                <a href="{{ route('performance.goals.edit', $goal->id) }}"
                   class="px-3 py-1 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <button onclick="deleteGoal({{ $goal->id }})"
                        class="px-3 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-bullseye text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No performance goals found</p>
            <p class="text-gray-400 text-sm mt-2">Create a new goal to get started</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($goals->hasPages())
    <div class="mt-6">
        {{ $goals->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function deleteGoal(id) {
    if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
        fetch(`/performance/goals/${id}`, {
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
            alert('An error occurred while deleting the goal');
        });
    }
}
</script>
@endpush
@endsection
