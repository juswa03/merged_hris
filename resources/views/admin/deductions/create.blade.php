@extends('layouts.app')

@section('title', 'Add Deduction')

@section('content')

<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Add New Deduction</h1>
        <p class="mt-2 text-sm text-gray-600">Create a new deduction type</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('deductions.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Deduction Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="deduction_type_id" class="block text-sm font-medium text-gray-700 mb-2">Deduction Type *</label>
                <select id="deduction_type_id" name="deduction_type_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('deduction_type_id') border-red-500 @enderror">
                    <option value="">Select Type</option>
                    @foreach($deductionTypes as $type)
                        <option value="{{ $type->id }}" {{ old('deduction_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('deduction_type_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Default Amount (₱) *</label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-500 @enderror">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">This is the default amount. You can set custom amounts when assigning to employees.</p>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('deductions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Deduction
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
