@extends('admin.layouts.app')
@section('title', 'Organization Chart')
@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header title="Organization Chart" description="Visual structure of departments, positions, and employees"/>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <x-admin.gradient-stat-card title="Departments" :value="$stats['departments']" icon="fas fa-building" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="Positions" :value="$stats['positions']" icon="fas fa-briefcase" gradientFrom="purple-500" gradientTo="purple-600"/>
        <x-admin.gradient-stat-card title="Employees" :value="$stats['employees']" icon="fas fa-users" gradientFrom="green-500" gradientTo="green-600"/>
    </div>

    <!-- Org Chart Container -->
    <div class="bg-white rounded-xl shadow-sm border p-6 overflow-x-auto">

        <!-- Root Node -->
        <div class="flex flex-col items-center mb-8">
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white rounded-xl px-8 py-4 shadow-lg text-center min-w-64">
                <i class="fas fa-university text-2xl mb-2 block opacity-80"></i>
                <div class="text-lg font-bold">{{ config('app.name') }}</div>
                <div class="text-xs opacity-75 mt-1">{{ $stats['employees'] }} Employees &bull; {{ $stats['departments'] }} Departments</div>
            </div>
            <!-- Connector line down -->
            <div class="w-0.5 h-8 bg-gray-300"></div>
        </div>

        @if($nodes->isEmpty())
            <p class="text-center text-gray-400 py-8">No departments found.</p>
        @else
        <!-- Departments Row -->
        <div class="relative">
            <!-- Horizontal line across top of dept cards -->
            <div class="absolute top-0 left-[calc(100%/(2*{{ $nodes->count() }}))] right-[calc(100%/(2*{{ $nodes->count() }}))] h-0.5 bg-gray-300"></div>

            <div class="grid gap-4" style="grid-template-columns: repeat({{ min($nodes->count(), 4) }}, minmax(0, 1fr))">
                @foreach($nodes as $dept)
                <div class="flex flex-col items-center">
                    <!-- Connector line down -->
                    <div class="w-0.5 h-6 bg-gray-300"></div>

                    <!-- Department Card -->
                    <div class="w-full border-2 border-blue-300 bg-blue-50 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-blue-600 text-white px-4 py-3 text-center">
                            <i class="fas fa-building text-sm mb-1 block opacity-75"></i>
                            <div class="text-sm font-bold leading-tight">{{ $dept['name'] }}</div>
                            <div class="text-xs opacity-75 mt-0.5">{{ $dept['employee_count'] }} employee(s)</div>
                        </div>

                        <!-- Positions -->
                        <div class="p-2 space-y-2">
                            @forelse($dept['positions'] as $position)
                            <div x-data="{ open: false }" class="border border-blue-200 rounded-lg overflow-hidden bg-white">
                                <button @click="open = !open"
                                        class="w-full flex items-center justify-between px-3 py-2 text-left hover:bg-blue-50 transition-colors">
                                    <div>
                                        <div class="text-xs font-semibold text-gray-700 leading-tight">{{ $position['name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ count($position['employees']) }} person(s)</div>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                </button>
                                <div x-show="open" x-transition class="border-t border-blue-100 bg-gray-50 px-3 py-2 space-y-1">
                                    @foreach($position['employees'] as $emp)
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <div class="w-5 h-5 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold flex-shrink-0 text-xs">
                                            {{ strtoupper(substr($emp['name'], 0, 1)) }}
                                        </div>
                                        <span class="truncate">{{ $emp['name'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <div class="text-xs text-gray-400 text-center py-2 italic">No employees assigned</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <p class="text-xs text-gray-400 text-center mt-4">
        <i class="fas fa-info-circle mr-1"></i>Click on a position to expand and see employee names.
    </p>
</div>
@endsection
