@extends('layouts.app')
@section('title', 'Allowance Details')
@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $allowance->name }}</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('allowances.assign', $allowance->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-user-plus mr-2"></i>Assign
            </a>
            <a href="{{ route('allowances.edit', $allowance->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('allowances.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">Back</a>
        </div>
    </div>
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Allowance Information</h2>
        <div class="grid grid-cols-3 gap-6">
            <div><p class="text-sm text-gray-600">Name</p><p class="text-lg font-medium">{{ $allowance->name }}</p></div>
            <div><p class="text-sm text-gray-600">Type</p><p class="text-lg font-medium">{{ ucfirst($allowance->type) }}</p></div>
            <div><p class="text-sm text-gray-600">Amount</p><p class="text-lg font-medium">₱{{ number_format($allowance->amount, 2) }}</p></div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold">Assigned Employees ({{ $employeeAllowances->count() }})</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Effective From</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Effective To</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($employeeAllowances as $empAll)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium">{{ $empAll->employee->full_name }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $empAll->employee->department->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center text-sm">{{ $empAll->effective_from ? $empAll->effective_from->format('M d, Y') : 'N/A' }}</td>
                    <td class="px-6 py-4 text-center text-sm">{{ $empAll->effective_to ? $empAll->effective_to->format('M d, Y') : 'Ongoing' }}</td>
                    <td class="px-6 py-4 text-center">
                        @if(!$empAll->effective_to || $empAll->effective_to >= now())
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Expired</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('allowances.removeAssignment', [$allowance->id, $empAll->employee->id]) }}" method="POST" class="inline"
                              onsubmit="return confirm('Remove allowance?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <p>No employees assigned</p>
                        <a href="{{ route('allowances.assign', $allowance->id) }}" class="text-blue-600 text-sm mt-2 inline-block">Assign now</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
