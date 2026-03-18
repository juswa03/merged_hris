@extends('admin.layouts.app')

@section('title', 'Permission Manager')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Role & Permission Manager</h2>
            <p class="text-sm text-gray-500 mt-1">
                Assign module permissions to each role. Click a cell to toggle, then save.
            </p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i> Back to Roles
        </a>
    </div>

    <form id="permForm" method="POST" action="{{ route('admin.roles.permissions.update') }}">
        @csrf

        {{-- Quick Actions --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-center">
            <span class="text-sm font-medium text-gray-600">Quick Actions:</span>
            <button type="button" onclick="toggleAll(true)" class="text-xs bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 px-3 py-1.5 rounded-lg">
                <i class="fas fa-check-square mr-1"></i> Check All
            </button>
            <button type="button" onclick="toggleAll(false)" class="text-xs bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100 px-3 py-1.5 rounded-lg">
                <i class="fas fa-square mr-1"></i> Uncheck All
            </button>
            <div class="ml-auto">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i> Save Permissions
                </button>
            </div>
        </div>

        {{-- Permission Matrix --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-5 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide w-64 sticky left-0 bg-gray-50 z-10">
                                Module / Permission
                            </th>
                            @foreach($roles as $role)
                            <th class="px-4 py-4 text-center min-w-[140px]">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs font-semibold text-gray-700">{{ $role->name }}</span>
                                    <button type="button"
                                        onclick="toggleColumn({{ $role->id }})"
                                        class="text-xs text-blue-500 hover:text-blue-700 underline">
                                        Toggle all
                                    </button>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $moduleName => $perms)
                        {{-- Module group header --}}
                        <tr class="bg-blue-600">
                            <td colspan="{{ $roles->count() + 1 }}" class="px-5 py-2">
                                <span class="text-xs font-bold text-white uppercase tracking-wider">
                                    <i class="fas fa-layer-group mr-2 opacity-70"></i>{{ $moduleName }}
                                </span>
                            </td>
                        </tr>
                        {{-- Permission rows --}}
                        @foreach($perms as $permKey => $permLabel)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors" data-perm="{{ $permKey }}">
                            <td class="px-5 py-3 text-gray-700 sticky left-0 bg-white hover:bg-gray-50 z-10">
                                <span class="font-medium">{{ $permLabel }}</span>
                                <span class="block text-xs text-gray-400 font-mono">{{ $permKey }}</span>
                            </td>
                            @foreach($roles as $role)
                            @php
                                $isGranted = isset($granted[$role->id]) && in_array($permKey, $granted[$role->id]);
                            @endphp
                            <td class="px-4 py-3 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer">
                                    <input type="checkbox"
                                        name="permissions[{{ $role->id }}][]"
                                        value="{{ $permKey }}"
                                        data-role="{{ $role->id }}"
                                        {{ $isGranted ? 'checked' : '' }}
                                        class="perm-checkbox w-5 h-5 rounded text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Sticky save bar --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-5 py-3 flex justify-between items-center">
                <p class="text-xs text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Changes are not saved until you click "Save Permissions".
                </p>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-6 py-2 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i> Save Permissions
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function toggleAll(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = state);
}

function toggleColumn(roleId) {
    const checkboxes = document.querySelectorAll(`.perm-checkbox[data-role="${roleId}"]`);
    // If all checked → uncheck all; otherwise check all
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}

// Highlight changed cells
document.querySelectorAll('.perm-checkbox').forEach(cb => {
    const original = cb.checked;
    cb.addEventListener('change', function () {
        const cell = this.closest('td');
        if (this.checked !== original) {
            cell.classList.add('bg-yellow-50');
        } else {
            cell.classList.remove('bg-yellow-50');
        }
    });
});
</script>
@endpush
