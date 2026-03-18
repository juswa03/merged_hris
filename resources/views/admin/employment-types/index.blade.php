@extends('admin.layouts.app')

@section('title', 'Employment Type Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Employment Type Management"
        description="Manage employment categories such as Permanent, Casual, Contractual, etc."
    >
        <x-slot name="actions">
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Type
            </button>
        </x-slot>
    </x-admin.page-header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.gradient-stat-card title="Total Types" :value="$stats['total']" icon="fas fa-id-badge" gradientFrom="blue-500" gradientTo="blue-600"/>
        <x-admin.gradient-stat-card title="In Use" :value="$stats['in_use']" icon="fas fa-users" gradientFrom="green-500" gradientTo="green-600"/>
        <x-admin.gradient-stat-card title="Unused" :value="$stats['unused']" icon="fas fa-ban" gradientFrom="gray-400" gradientTo="gray-500"/>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <!-- Table -->
    <x-admin.card :padding="false">
        <x-admin.table-wrapper>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Employees</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($types as $type)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $types->firstItem() + $loop->index }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-id-badge mr-2 text-blue-500"></i>{{ $type->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $type->description ?? '—' }}</td>
                    <td class="px-6 py-4 text-center text-sm font-semibold text-gray-700">{{ $type->employees_count }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openEditModal({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ addslashes($type->description ?? '') }}')"
                                    class="text-blue-600 hover:text-blue-800 px-3 py-1 rounded text-sm border border-blue-200 hover:bg-blue-50">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            @if($type->employees_count === 0)
                            <form action="{{ route('admin.employment-types.destroy', $type) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ addslashes($type->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 px-3 py-1 rounded text-sm border border-red-200 hover:bg-red-50">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                            @else
                            <span class="text-gray-300 text-xs italic">In use</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-id-badge text-4xl mb-3 block"></i>
                        No employment types found. <button onclick="openCreateModal()" class="text-blue-600 hover:underline">Add the first one.</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-admin.table-wrapper>
        <div class="px-6 py-4 border-t">{{ $types->links() }}</div>
    </x-admin.card>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Add Employment Type</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.employment-types.store') }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Permanent, Casual, Contractual"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">Common: Permanent, Casual, Contractual, Job Order, Co-Terminous</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2" placeholder="Optional description..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i>Create
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Edit Employment Type</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="editForm" method="POST" class="p-6">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type Name <span class="text-red-500">*</span></label>
                <input type="text" id="edit_name" name="name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="edit_description" name="description" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }
function openEditModal(id, name, description) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('editForm').action = '/admin/employment-types/' + id;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
</script>
@endpush
@endsection
