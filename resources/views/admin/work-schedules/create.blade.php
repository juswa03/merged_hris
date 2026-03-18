@extends('admin.layouts.app')

@section('title', 'Create Work Schedule')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <x-admin.page-header
        title="Create Work Schedule"
        description="Define a new work schedule for employees"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.work-schedules.index') }}" variant="secondary" icon="fas fa-arrow-left">
                Back
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    @if($errors->any())
        <x-admin.alert type="error" dismissible class="mb-6">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </x-admin.alert>
    @endif

    <x-admin.card>
        <form action="{{ route('admin.work-schedules.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-400 @enderror"
                           placeholder="e.g. Regular Office Hours, Night Shift">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Type <span class="text-red-500">*</span></label>
                    <select name="type" required id="scheduleType"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('type') border-red-400 @enderror">
                        <option value="">Select type...</option>
                        <option value="regular" {{ old('type') === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="flexible" {{ old('type') === 'flexible' ? 'selected' : '' }}>Flexible</option>
                        <option value="shift" {{ old('type') === 'shift' ? 'selected' : '' }}>Shift</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Break Minutes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Break Duration (minutes)</label>
                    <input type="number" name="break_minutes" value="{{ old('break_minutes', 60) }}" min="0" max="480"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('break_minutes') border-red-400 @enderror">
                    @error('break_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Work Start -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Work Start Time <span class="text-red-500">*</span></label>
                    <input type="time" name="work_start" value="{{ old('work_start', '08:00') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('work_start') border-red-400 @enderror">
                    @error('work_start')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Work End -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Work End Time <span class="text-red-500">*</span></label>
                    <input type="time" name="work_end" value="{{ old('work_end', '17:00') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('work_end') border-red-400 @enderror">
                    @error('work_end')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Working Days -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Working Days <span class="text-red-500">*</span></label>
                    @php
                        $days = [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun'];
                        $oldDays = old('working_days', [1,2,3,4,5]);
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach($days as $num => $label)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="working_days[]" value="{{ $num }}" class="sr-only peer"
                                   {{ in_array($num, array_map('intval', (array)$oldDays)) ? 'checked' : '' }}>
                            <span class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600
                                         peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white
                                         hover:border-blue-400 transition-colors select-none">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('working_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 @error('description') border-red-400 @enderror"
                              placeholder="Optional description or notes about this schedule">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Active -->
                <div class="md:col-span-2 flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Active (available for assignment)</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.work-schedules.index') }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Schedule
                </button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
