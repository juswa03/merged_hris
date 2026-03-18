@extends('admin.layouts.app')

@section('title', 'Performance Goals')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="Performance Goals"
        description="Set and track employee performance goals"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.performance.goals.create') }}" icon="fas fa-plus">
                New Goal
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-admin.gradient-stat-card
            title="Total Goals"
            :value="$stats['total_goals']"
            icon="fas fa-bullseye"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Active Goals"
            :value="$stats['active_goals']"
            icon="fas fa-tasks"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
        <x-admin.gradient-stat-card
            title="Completed Goals"
            :value="$stats['completed_goals']"
            icon="fas fa-check-circle"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Overdue Goals"
            :value="$stats['overdue_goals']"
            icon="fas fa-exclamation-triangle"
            gradientFrom="red-500"
            gradientTo="red-600"
        />
    </div>

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" action="{{ route('admin.performance.goals.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                       placeholder="Search goals or employees...">
            </div>
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select name="employee_id" id="employee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Not Started</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select name="priority" id="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="flex items-end">
                <x-admin.action-button type="submit" variant="primary" icon="fas fa-filter" class="w-full justify-center">
                    Filter
                </x-admin.action-button>
            </div>
        </form>
    </x-admin.card>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($goals as $goal)
        <x-admin.card title="{{ $goal->title }}" class="hover:shadow-lg transition-shadow">
            <x-slot name="headerActions">
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
            </x-slot>

            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-user mr-1"></i> {{ $goal->employee->full_name }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-building mr-1"></i> {{ $goal->employee->department->name ?? 'No Dept' }}
                </p>

                @if($goal->description)
                <p class="text-sm text-gray-700 line-clamp-2">{{ $goal->description }}</p>
                @endif
            </div>

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
            <div class="flex items-center justify-between text-sm text-gray-600">
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

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-2">
                    <x-admin.action-button href="{{ route('admin.performance.goals.show', $goal->id) }}" variant="info" size="sm" icon="fas fa-eye">
                        View
                    </x-admin.action-button>
                    <x-admin.action-button href="{{ route('admin.performance.goals.edit', $goal->id) }}" variant="success" size="sm" icon="fas fa-edit">
                        Edit
                    </x-admin.action-button>
                    <x-admin.action-button onclick="deleteGoal({{ $goal->id }})" variant="danger" size="sm" icon="fas fa-trash">
                        Delete
                    </x-admin.action-button>
                </div>
            </x-slot>
        </x-admin.card>
        @empty
        <div class="col-span-2">
            <x-admin.empty-state
                icon="fas fa-bullseye"
                title="No performance goals found"
                description="Create a new goal to get started"
            />
        </div>
        @endforelse
    </div>

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
