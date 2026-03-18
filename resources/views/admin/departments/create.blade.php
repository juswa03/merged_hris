@extends('admin.layouts.app')

@section('title', 'Create Department')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <x-admin.page-header
        title="Create New Department"
        description="Add a new department to your organization"
    >
        <x-slot name="actions">
            <x-admin.action-button :href="route('admin.departments.index')" variant="secondary" icon="fas fa-arrow-left">
                Back to Departments
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <x-admin.card>
                <form action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf

                    <!-- Department Name -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Department Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-500 @enderror"
                            placeholder="e.g., Human Resources"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i> Enter a unique department name.
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('description') border-red-500 @enderror"
                            placeholder="Brief description of the department's role and responsibilities..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i> Optional: Add details about this department.
                        </p>
                    </div>

                    <!-- Error Alert -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 font-medium">
                                        Please correct the errors above and try again.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
                        <x-admin.action-button :href="route('admin.departments.index')" variant="secondary" icon="fas fa-times">
                            Cancel
                        </x-admin.action-button>
                        <x-admin.action-button type="submit" variant="primary" icon="fas fa-save">
                            Create Department
                        </x-admin.action-button>
                    </div>
                </form>
            </x-admin.card>
        </div>

        <!-- Info Card -->
        <div class="lg:col-span-1">
            <x-admin.card title="Quick Tips" class="bg-blue-50 border border-blue-100">
                <div class="text-sm text-blue-800 space-y-3">
                    <ul class="list-disc list-inside space-y-2">
                        <li>Department names must be <strong>unique</strong> across your organization.</li>
                        <li>You can assign employees to this department after creation.</li>
                        <li>Empty departments can be deleted at any time.</li>
                        <li>Use descriptive summaries for better clarity.</li>
                    </ul>
                </div>
            </x-admin.card>
        </div>
    </div>
</div>
@endsection
