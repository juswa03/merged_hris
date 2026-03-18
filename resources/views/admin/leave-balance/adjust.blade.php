@extends('admin.layouts.app')

@section('title', 'Adjust Leave Balance - {{ $employee->full_name }}')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <x-admin.page-header
        title="Adjust Leave Balance"
        description="{{ $employee->full_name }} — {{ $employee->employee_id }}"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.leave-balance.show', $employee) }}" variant="secondary" icon="fas fa-arrow-left">
                Back to Details
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    @if($errors->any())
        <x-admin.alert type="error" dismissible class="mb-6">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-admin.alert>
    @endif

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Employee Quick Info -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-center gap-4">
        @if($employee->photo_url)
            <img src="{{ asset('storage/'.$employee->photo_url) }}" class="w-12 h-12 rounded-full object-cover" alt="">
        @else
            <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold">
                {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
            </div>
        @endif
        <div>
            <div class="font-semibold text-gray-800">{{ $employee->full_name }}</div>
            <div class="text-xs text-blue-600">{{ $employee->department->name ?? '' }} &bull; {{ $employee->position->name ?? '' }}</div>
        </div>
    </div>

    <x-admin.card>
        <form action="{{ route('admin.leave-balance.save-adjustment', $employee) }}" method="POST" class="space-y-5" id="adjustForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                    <select name="year" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('year') border-red-400 @enderror">
                        @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Leave Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type" required id="leaveTypeSelect"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('leave_type') border-red-400 @enderror">
                        @foreach(\App\Models\LeaveBalance::LEAVE_TYPES as $type => $label)
                            <option value="{{ $type }}" {{ old('leave_type') === $type ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('leave_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Opening Balance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                    <input type="number" name="opening_balance" step="0.001" min="0" id="openingBalance"
                           value="{{ old('opening_balance', 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('opening_balance') border-red-400 @enderror"
                           oninput="calcClosing()">
                    @error('opening_balance')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Earned -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Earned</label>
                    <input type="number" name="earned" step="0.001" min="0" id="earnedBalance"
                           value="{{ old('earned', 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('earned') border-red-400 @enderror"
                           oninput="calcClosing()">
                    @error('earned')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Used -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Used</label>
                    <input type="number" name="used" step="0.001" min="0" id="usedBalance"
                           value="{{ old('used', 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('used') border-red-400 @enderror"
                           oninput="calcClosing()">
                    @error('used')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Closing Balance (auto-computed) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Closing Balance</label>
                    <div class="relative">
                        <input type="number" name="closing_balance" step="0.001" id="closingBalance"
                               value="{{ old('closing_balance', 0) }}" readonly
                               class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 font-semibold text-blue-700 @error('closing_balance') border-red-400 @enderror">
                        <span class="absolute right-3 top-2.5 text-gray-400 text-xs">auto</span>
                    </div>
                    @error('closing_balance')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                <i class="fas fa-info-circle mr-1"></i>
                Closing Balance = Opening Balance + Earned − Used. This creates or updates the balance record for the selected year and leave type.
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.leave-balance.show', $employee) }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Adjustment
                </button>
            </div>
        </form>
    </x-admin.card>
</div>

<script>
function calcClosing() {
    const opening = parseFloat(document.getElementById('openingBalance').value) || 0;
    const earned  = parseFloat(document.getElementById('earnedBalance').value)  || 0;
    const used    = parseFloat(document.getElementById('usedBalance').value)    || 0;
    document.getElementById('closingBalance').value = (opening + earned - used).toFixed(3);
}
calcClosing();
</script>
@endsection
