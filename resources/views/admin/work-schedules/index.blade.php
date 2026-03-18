@extends('admin.layouts.app')

@section('title', 'Work Schedule Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Work Schedule Management"
        description="Define regular, flexible, and shift-based work schedules for employees"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.work-schedules.create') }}" variant="primary" icon="fas fa-plus">
                New Schedule
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Schedules" :value="$stats['total']" icon="fas fa-clock" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Active" :value="$stats['active']" icon="fas fa-check-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Inactive" :value="$stats['inactive']" icon="fas fa-pause-circle" gradientFrom="red-500" gradientTo="red-600"/>
    </div>

    {{-- Force Tailwind to generate gradient classes --}}
    <div class="hidden from-blue-500 to-blue-600 from-green-500 to-green-600 from-red-500 to-red-600"></div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <!-- Schedules Grid -->
    @if($schedules->isEmpty())
        <x-admin.empty-state
            icon="fas fa-clock"
            title="No work schedules defined yet"
            message="Create your first work schedule to start managing employee shifts."
            actionText="Create First Schedule"
            :actionLink="route('admin.work-schedules.create')"
        />
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($schedules as $schedule)
        <x-admin.card padding="false" class="flex flex-col h-full hover:shadow-lg transition-shadow duration-200">
            <!-- Card Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg flex-shrink-0 shadow-sm
                        {{ $schedule->type === 'regular' ? 'bg-blue-100' : ($schedule->type === 'flexible' ? 'bg-purple-100' : 'bg-orange-100') }}">
                        <i class="fas {{ $schedule->type === 'regular' ? 'fa-clock text-blue-700' : ($schedule->type === 'flexible' ? 'fa-random text-purple-700' : 'fa-exchange-alt text-orange-700') }}"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 truncate" title="{{ $schedule->name }}">{{ $schedule->name }}</h3>
                        <span class="text-xs text-gray-500 capitalize block">{{ $schedule->type }} Schedule</span>
                    </div>
                </div>
                <x-admin.badge :variant="$schedule->is_active ? 'success' : 'default'">
                    {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                </x-admin.badge>
            </div>

            <!-- Card Body -->
            <div class="px-6 py-4 space-y-4 flex-grow">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-clock text-gray-400 w-4 text-center"></i>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->work_start)->format('h:i A') }}
                            —
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->work_end)->format('h:i A') }}
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $schedule->hours_per_day }}h/day</span>
                </div>
                
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-coffee text-gray-400 w-4 text-center"></i>
                    <span>{{ $schedule->break_minutes }} min break</span>
                </div>
                
                <div class="flex items-start gap-2 text-sm text-gray-600">
                    <i class="fas fa-calendar-week text-gray-400 w-4 text-center mt-0.5"></i>
                    <span class="leading-relaxed">{{ Str::limit($schedule->working_days_label, 40) }}</span>
                </div>
                
                @if($schedule->description)
                <div class="flex items-start gap-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle text-gray-400 w-4 text-center mt-0.5"></i>
                    <span class="text-xs leading-relaxed line-clamp-2">{{ $schedule->description }}</span>
                </div>
                @endif
            </div>
            
            <div class="px-6 py-2 bg-gray-50 border-t border-gray-100">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-users text-gray-400"></i>
                    <span>{{ $schedule->employees_count }} employee(s) assigned</span>
                </div>
            </div>

            <!-- Card Footer (Actions) -->
            <div class="px-6 py-4 border-t border-gray-100 bg-white rounded-b-lg">
                <div class="flex items-center gap-2">
                    <x-admin.action-button href="{{ route('admin.work-schedules.edit', $schedule) }}" 
                                           variant="secondary" 
                                           size="sm" 
                                           class="flex-1 justify-center relative group" 
                                           icon="fas fa-edit">
                        Edit
                    </x-admin.action-button>

                    <form action="{{ route('admin.work-schedules.toggle-status', $schedule) }}" method="POST" class="flex-1">
                        @csrf
                        <x-admin.action-button type="submit" 
                                               variant="{{ $schedule->is_active ? 'warning' : 'success' }}" 
                                               size="sm" 
                                               class="w-full justify-center" 
                                               icon="{{ $schedule->is_active ? 'fas fa-pause' : 'fas fa-play' }}">
                            {{ $schedule->is_active ? 'Deactivate' : 'Activate' }}
                        </x-admin.action-button>
                    </form>

                    @if($schedule->employees_count === 0)
                        <form action="{{ route('admin.work-schedules.destroy', $schedule) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete {{ addslashes($schedule->name) }}?');">
                            @csrf @method('DELETE')
                            <x-admin.action-button type="submit" variant="danger" size="sm" icon="fas fa-times" iconOnly />
                        </form>
                    @endif
                </div>
            </div>
        </x-admin.card>
        @endforeach
    </div>

    <div class="mt-6">{{ $schedules->links() }}</div>
    @endif
</div>
@endsection
