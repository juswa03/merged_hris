@extends('admin.layouts.app')

@section('title', 'Job Status Management')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ 
    editId: null, 
    editName: '',
    createName: '' 
}">
    <x-admin.page-header
        title="Job Status Management"
        description="Manage employee job statuses such as Active, On Leave, Terminated, etc."
    >
        <x-slot name="actions">
            <x-admin.action-button 
                @click="$dispatch('open-modal-createJobStatus')" 
                variant="primary" 
                icon="plus"
            >
                Add Status
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Statuses" :value="$stats['total']" icon="fas fa-tags" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="In Use" :value="$stats['in_use']" icon="fas fa-check-circle" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Unused" :value="$stats['unused']" icon="fas fa-pause-circle" gradientFrom="gray-600" gradientTo="gray-700"/>
    </div>

    {{-- Force Tailwind to generate gradient classes --}}
    <div class="hidden from-blue-500 to-blue-600 from-green-500 to-green-600 from-gray-600 to-gray-700"></div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <!-- Table Card -->
    <x-admin.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Name</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobStatuses as $status)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 w-16 text-center">
                            {{ $jobStatuses->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeClass = match($status->name) {
                                    'Active' => 'bg-green-100 text-green-800',
                                    'On Leave' => 'bg-yellow-100 text-yellow-800',
                                    'Terminated', 'Inactive', 'Suspended' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $status->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ $status->employees_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <x-admin.action-button 
                                    @click="editId = {{ $status->id }}; editName = '{{ addslashes($status->name) }}'; $dispatch('open-modal-editJobStatus')" 
                                    variant="secondary" 
                                    size="sm" 
                                    icon="edit"
                                >
                                    Edit
                                </x-admin.action-button>

                                @if($status->employees_count === 0)
                                    <form action="{{ route('admin.job-statuses.destroy', $status) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete {{ addslashes($status->name) }}?');" 
                                          class="inline-block">
                                        @csrf @method('DELETE')
                                        <x-admin.action-button type="submit" variant="danger" size="sm" icon="trash">
                                            Delete
                                        </x-admin.action-button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic px-2 py-1 select-none cursor-help" title="Cannot delete: Status is currently assigned to employees">
                                        In Use
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <x-admin.empty-state
                                icon="fas fa-tags"
                                title="No job statuses found"
                                message="Start by creating your first job status."
                                actionText="Add Status"
                                :buttonAction="true"
                            >
                                <x-slot name="action">
                                    <x-admin.action-button @click="$dispatch('open-modal-createJobStatus')" variant="primary" icon="plus">
                                        Add Status
                                    </x-admin.action-button>
                                </x-slot>
                            </x-admin.empty-state>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($jobStatuses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $jobStatuses->links() }}
            </div>
        @endif
    </x-admin.card>

    <!-- Create Modal -->
    <x-admin.modal name="createJobStatus" title="Add New Job Status" maxWidth="md">
        <form action="{{ route('admin.job-statuses.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="create_name" class="block text-sm font-medium text-gray-700">Status Name <span class="text-red-500">*</span></label>
                <input type="text" id="create_name" name="name" required x-model="createName"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                       placeholder="e.g. Active, On Leave, Retired">
                <p class="mt-1 text-xs text-gray-500">Common examples: Active, Probational, Regular, Terminated</p>
            </div>
            <div class="flex justify-end pt-2 space-x-3">
                <x-admin.action-button type="button" variant="secondary" @click="$dispatch('close-modal-createJobStatus')">
                    Cancel
                </x-admin.action-button>
                <x-admin.action-button type="submit" variant="primary" icon="plus">
                    Create Status
                </x-admin.action-button>
            </div>
        </form>
    </x-admin.modal>

    <!-- Edit Modal -->
    <x-admin.modal name="editJobStatus" title="Edit Job Status" maxWidth="md">
        <form x-bind:action="'{{ route('admin.job-statuses.index') }}/' + editId" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="edit_name" class="block text-sm font-medium text-gray-700">Status Name <span class="text-red-500">*</span></label>
                <input type="text" id="edit_name" name="name" required x-model="editName"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div class="flex justify-end pt-2 space-x-3">
                <x-admin.action-button type="button" variant="secondary" @click="$dispatch('close-modal-editJobStatus')">
                    Cancel
                </x-admin.action-button>
                <x-admin.action-button type="submit" variant="primary" icon="save">
                    Update Status
                </x-admin.action-button>
            </div>
        </form>
    </x-admin.modal>
</div>
@endsection
