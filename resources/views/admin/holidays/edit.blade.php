@extends('admin.layouts.app')

@section('title', 'Edit Holiday')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.holidays.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Holiday</h1>
        </div>
        <p class="text-sm text-gray-600 ml-8">Update the details for <strong>{{ $holiday->name }}</strong></p>
    </div>

    <!-- Form Card -->
    <div class="max-w-2xl bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.holidays.update', $holiday) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Holiday Name -->
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Holiday Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $holiday->name) }}"
                       class="block w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500
                              @error('name') border-red-500 @else border-gray-300 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date -->
            <div class="mb-5">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                    Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date" name="date"
                       value="{{ old('date', $holiday->date->format('Y-m-d')) }}"
                       class="block w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500
                              @error('date') border-red-500 @else border-gray-300 @enderror"
                       required>
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="mb-5">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type <span class="text-red-500">*</span>
                </label>
                <select id="type" name="type"
                        class="block w-full px-4 py-2 border rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500
                               @error('type') border-red-500 @else border-gray-300 @enderror"
                        required>
                    <option value="regular"  {{ old('type', $holiday->type) === 'regular'  ? 'selected' : '' }}>Regular Holiday</option>
                    <option value="special"  {{ old('type', $holiday->type) === 'special'  ? 'selected' : '' }}>Special Non-Working Holiday</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Paid -->
            <div class="mb-5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_paid" value="0">
                    <input type="checkbox" id="is_paid" name="is_paid" value="1"
                           {{ old('is_paid', $holiday->is_paid) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Paid Holiday</span>
                </label>
                <p class="mt-1 text-xs text-gray-500 ml-6">Employees receive pay for this holiday</p>
            </div>

            <!-- Remarks -->
            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                    Remarks
                </label>
                <textarea id="remarks" name="remarks" rows="3"
                          class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500
                                 @error('remarks') border-red-500 @enderror"
                          placeholder="Optional notes about this holiday...">{{ old('remarks', $holiday->remarks) }}</textarea>
                @error('remarks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if($errors->any())
            <div class="mb-5 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <p class="text-sm text-red-700">Please correct the errors above and try again.</p>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200">
                <a href="{{ route('admin.holidays.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-1"></i> Update Holiday
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
