@extends('admin.layouts.app')

@section('title', 'Deduction Details')

@section('content')

<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $deduction->name }}</h1>
                <p class="mt-2 text-sm text-gray-600">Deduction details and assignments</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.deductions.assign', $deduction->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Assign to Employees
                </a>
                <a href="{{ route('admin.deductions.edit', $deduction->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.deductions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-check-circle text-green-400 mr-3"></i>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Deduction Info Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Deduction Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Deduction Name</p>
                <p class="text-lg font-medium text-gray-900">{{ $deduction->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Type</p>
                <p class="text-lg font-medium text-gray-900">{{ $deduction->deductionType->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Default Amount</p>
                <p class="text-lg font-medium text-gray-900">₱{{ number_format($deduction->amount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Assigned Employees</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ $employeeDeductions->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Assignments</p>
                    <p class="text-3xl font-semibold text-gray-900">
                        {{ $employeeDeductions->filter(fn($ed) => !$ed->effective_to || $ed->effective_to >= now())->count() }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Monthly Amount</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        ₱{{ number_format($employeeDeductions->sum(fn($ed) => $ed->custom_amount ?? $deduction->amount), 2) }}
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Employees Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Assigned Employees</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Effective From</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Effective To</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employeeDeductions as $empDed)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $empDed->employee->full_name }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $empDed->employee->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $empDed->employee->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                            ₱{{ number_format($empDed->custom_amount ?? $deduction->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                            {{ $empDed->effective_from ? $empDed->effective_from->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                            {{ $empDed->effective_to ? $empDed->effective_to->format('M d, Y') : 'Ongoing' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if(!$empDed->effective_to || $empDed->effective_to >= now())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Expired
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <form action="{{ route('admin.deductions.removeAssignment', [$deduction->id, $empDed->employee->id]) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Remove this deduction from {{ $empDed->employee->full_name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users-slash text-4xl mb-2"></i>
                            <p>No employees assigned to this deduction yet</p>
                            <a href="{{ route('admin.deductions.assign', $deduction->id) }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                <i class="fas fa-user-plus mr-1"></i>Assign employees now
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
