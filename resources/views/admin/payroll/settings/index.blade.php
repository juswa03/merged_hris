@extends('admin.layouts.app')

@section('title', 'Payroll Settings')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ activeTab: new URLSearchParams(window.location.search).get('tab') || 'general' }">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Payroll Configuration</h1>
        <p class="mt-2 text-sm text-gray-600">Manage global settings, deduction types, and specific deductions.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
        <div class="flex">
            <i class="fas fa-check-circle text-green-400 mr-3"></i>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'general'" 
                    :class="activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                General Settings
            </button>
            <button @click="activeTab = 'types'" 
                    :class="activeTab === 'types' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Deduction Types
            </button>
            <button @click="activeTab = 'deductions'" 
                    :class="activeTab === 'deductions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Deductions List
            </button>
        </nav>
    </div>

    <!-- Tab 1: General Settings -->
    <div x-show="activeTab === 'general'" class="space-y-6">
        <form action="{{ route('admin.payroll.settings.update') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- GSIS Settings -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h2 class="text-lg font-semibold text-blue-800">GSIS Configuration</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($settings['gsis'] ?? [] as $setting)
                        <div>
                            <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->name }}</label>
                            <input type="number" step="0.0001" name="{{ $setting->key }}" id="{{ $setting->key }}" 
                                   value="{{ $setting->value }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- PhilHealth Settings -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                        <h2 class="text-lg font-semibold text-green-800">PhilHealth Configuration</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($settings['philhealth'] ?? [] as $setting)
                        <div>
                            <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->name }}</label>
                            <input type="number" step="0.0001" name="{{ $setting->key }}" id="{{ $setting->key }}" 
                                   value="{{ $setting->value }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pag-IBIG Settings -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
                        <h2 class="text-lg font-semibold text-yellow-800">Pag-IBIG Configuration</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($settings['pagibig'] ?? [] as $setting)
                        <div>
                            <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->name }}</label>
                            <input type="number" step="0.0001" name="{{ $setting->key }}" id="{{ $setting->key }}" 
                                   value="{{ $setting->value }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 2: Deduction Types -->
    <div x-show="activeTab === 'types'" class="space-y-6" style="display: none;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Deduction Types</h2>
                <p class="mt-1 text-sm text-gray-600">Manage categories for employee deductions</p>
            </div>
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>Add Type
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions Count</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deductionTypes as $type)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $type->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $type->description ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $type->deductions_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="openEditModal({{ $type->id }}, '{{ $type->name }}', '{{ $type->description }}')" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if($type->deductions_count == 0)
                            <form action="{{ route('admin.deduction-types.destroy', $type) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this type?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 cursor-not-allowed" title="Cannot delete type with associated deductions">
                                <i class="fas fa-trash"></i>
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No deduction types found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab 3: Deductions List -->
    <div x-show="activeTab === 'deductions'" class="space-y-6" style="display: none;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Deductions List</h2>
                <p class="mt-1 text-sm text-gray-600">Manage specific deductions and their amounts</p>
            </div>
            <a href="{{ route('admin.deductions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i>Add Deduction
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" action="{{ route('admin.payroll.settings.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Preserve active tab -->
                <input type="hidden" name="tab" value="deductions">
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search deduction name..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="type" name="type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        @foreach($deductionTypes as $type)
                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.payroll.settings.index', ['tab' => 'deductions']) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Deductions Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deductions as $deduction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $deduction->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $deduction->deductionType->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            ₱{{ number_format($deduction->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.deductions.edit', $deduction->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.deductions.show', $deduction->id) }}" class="text-green-600 hover:text-green-900 mr-3" title="View Assignments">
                                <i class="fas fa-users"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No deductions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $deductions->appends(['tab' => 'deductions', 'search' => request('search'), 'type' => request('type')])->links() }}
            </div>
        </div>
    </div>


</div>

<!-- Create/Edit Modal for Deduction Types -->
<div id="typeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="typeForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Add Deduction Type</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add Deduction Type';
        document.getElementById('typeForm').action = "{{ route('admin.deduction-types.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('typeModal').classList.remove('hidden');
    }

    function openEditModal(id, name, description) {
        document.getElementById('modalTitle').innerText = 'Edit Deduction Type';
        document.getElementById('typeForm').action = `/deduction-types/${id}`;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        document.getElementById('name').value = name;
        document.getElementById('description').value = description || '';
        document.getElementById('typeModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('typeModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
