@extends('admin.layouts.app')

@section('title', 'Leave Balance Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Leave Balance Management"
        description="View and manage employee leave credits and balances"
    />

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Employees" :value="$stats['total_employees']" icon="fas fa-users" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="With Balances" :value="$stats['with_balances']" icon="fas fa-check-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="No Records Yet" :value="$stats['without_balances']" icon="fas fa-exclamation-circle" gradientFrom="yellow-400" gradientTo="yellow-500"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Filters -->
    <x-admin.card class="mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Search Employee</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name or employee ID..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-40">
                <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $id => $name)
                        <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-28">
                <label class="block text-xs font-medium text-gray-600 mb-1">Year</label>
                <select name="year" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-search mr-1"></i>Filter
            </button>
            <a href="{{ route('admin.leave-balance.index') }}" class="px-4 py-2 border border-gray-300 text-sm rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </form>
    </x-admin.card>

    <!-- Table -->
    <x-admin.card>
        <x-admin.table-wrapper>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" title="Vacation Leave">
                            VL<br><span class="text-gray-400 font-normal normal-case">Vacation Leave</span>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" title="Sick Leave">
                            SL<br><span class="text-gray-400 font-normal normal-case">Sick Leave</span>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" title="Forced Leave">
                            FL<br><span class="text-gray-400 font-normal normal-case">Forced Leave</span>
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                    @php
                        $empBalances = $balances->get($employee->id, collect());
                        $vl = $empBalances->where('leave_type','vacation')->first();
                        $sl = $empBalances->where('leave_type','sick')->first();
                        $fl = $empBalances->where('leave_type','forced')->first();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($employee->photo_url)
                                    <img src="{{ asset('storage/'.$employee->photo_url) }}" class="w-8 h-8 rounded-full object-cover" alt="">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                                        {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                    <div class="text-xs text-gray-500">#{{ $employee->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $employee->department->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($vl)
                                <span class="text-sm font-semibold {{ $vl->closing_balance > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ number_format($vl->closing_balance, 3) }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($sl)
                                <span class="text-sm font-semibold {{ $sl->closing_balance > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                    {{ number_format($sl->closing_balance, 3) }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($fl)
                                <span class="text-sm font-semibold {{ $fl->closing_balance > 0 ? 'text-purple-600' : 'text-gray-400' }}">
                                    {{ number_format($fl->closing_balance, 3) }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.leave-balance.show', $employee) }}"
                                   class="px-3 py-1 text-xs bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('admin.leave-balance.adjust', $employee) }}"
                                   class="px-3 py-1 text-xs bg-gray-50 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Adjust
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-search text-3xl mb-3 block"></i>
                            No employees found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrapper>
        <div class="p-4">{{ $employees->withQueryString()->links() }}</div>
        <div class="px-4 pb-4 flex flex-wrap gap-4 text-xs text-gray-400 border-t pt-3">
            <span><strong class="text-gray-600">VL</strong> — Vacation Leave</span>
            <span><strong class="text-gray-600">SL</strong> — Sick Leave</span>
            <span><strong class="text-gray-600">FL</strong> — Forced Leave</span>
            <span class="ml-auto italic">Values shown are closing balances for the selected year.</span>
        </div>
    </x-admin.card>
</div>
@endsection
