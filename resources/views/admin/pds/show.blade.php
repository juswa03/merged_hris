@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="PDS — {{ $pds->employee->full_name ?? 'Employee' }}"
        description="{{ $pds->employee->position->name ?? '' }} · {{ $pds->employee->department->name ?? '' }}"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.pds.index') }}" variant="secondary" icon="fas fa-arrow-left">
                Back
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif
    @if(session('error'))
        <x-admin.alert type="error" dismissible class="mb-6">{{ session('error') }}</x-admin.alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Status & Actions --}}
        <div class="lg:col-span-1 space-y-6">
            <x-admin.card title="PDS Status">
                @php
                    $statusVariant = match($pds->status) {
                        'verified'     => 'success',
                        'submitted'    => 'warning',
                        'under_review' => 'info',
                        'rejected'     => 'danger',
                        default        => 'default',
                    };
                @endphp
                <div class="mb-4">
                    <x-admin.badge :variant="$statusVariant" class="text-sm px-3 py-1">
                        {{ $pds->getStatusDisplay() }}
                    </x-admin.badge>
                </div>

                <dl class="space-y-2 text-sm">
                    @if($pds->submitted_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Submitted</dt>
                            <dd class="font-medium text-gray-700">{{ $pds->submitted_at->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    @if($pds->verified_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Verified</dt>
                            <dd class="font-medium text-gray-700">{{ $pds->verified_at->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    @if($pds->rejected_at)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Rejected</dt>
                            <dd class="font-medium text-gray-700">{{ $pds->rejected_at->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    @if($pds->lastActionBy)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Last action by</dt>
                            <dd class="font-medium text-gray-700">{{ $pds->lastActionBy->name ?? '—' }}</dd>
                        </div>
                    @endif
                </dl>

                @if($pds->verification_remarks)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-700">
                        <p class="font-medium text-gray-600 mb-1">Remarks</p>
                        {{ $pds->verification_remarks }}
                    </div>
                @endif
            </x-admin.card>

            {{-- Workflow Actions --}}
            <x-admin.card title="Actions">
                <div class="space-y-3">
                    @if($pds->status === 'submitted')
                        <form action="{{ route('admin.pds.mark-under-review', $pds) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                                <i class="fas fa-search mr-2"></i> Mark Under Review
                            </button>
                        </form>
                    @endif

                    @if(in_array($pds->status, ['submitted', 'under_review']))
                        <button
                            type="button"
                            onclick="document.getElementById('verifyModal').classList.remove('hidden')"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition"
                        >
                            <i class="fas fa-check-circle mr-2"></i> Verify PDS
                        </button>

                        <button
                            type="button"
                            onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition"
                        >
                            <i class="fas fa-times-circle mr-2"></i> Reject PDS
                        </button>
                    @endif

                    <form
                        action="{{ route('admin.pds.destroy', $pds) }}"
                        method="POST"
                        onsubmit="return confirm('Delete this PDS record? This cannot be undone.')"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full border border-red-300 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition">
                            <i class="fas fa-trash mr-2"></i> Delete Record
                        </button>
                    </form>
                </div>
            </x-admin.card>
        </div>

        {{-- PDS Data --}}
        <div class="lg:col-span-2 space-y-6">
            @if($pds->data)
                @foreach($pds->data as $section => $fields)
                    <x-admin.card :title="ucwords(str_replace('_', ' ', $section))">
                        @if(is_array($fields))
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                @foreach($fields as $key => $value)
                                    <div>
                                        <dt class="text-gray-500 capitalize mb-0.5">{{ str_replace('_', ' ', $key) }}</dt>
                                        <dd class="font-medium text-gray-800 break-words">
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @else
                                                {{ $value ?: '—' }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        @else
                            <p class="text-sm text-gray-700">{{ $fields }}</p>
                        @endif
                    </x-admin.card>
                @endforeach
            @else
                <x-admin.card>
                    <x-admin.empty-state
                        icon="fas fa-file-alt"
                        title="No PDS data yet"
                        message="The employee has not filled in their PDS."
                    />
                </x-admin.card>
            @endif
        </div>
    </div>
</div>

{{-- Verify Modal --}}
<div id="verifyModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Verify PDS</h3>
        <form action="{{ route('admin.pds.verify', $pds) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                <textarea name="remarks" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Add any verification notes…"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('verifyModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">Verify</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Reject PDS</h3>
        <form action="{{ route('admin.pds.reject', $pds) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection <span class="text-red-500">*</span></label>
                <textarea name="remarks" rows="3" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Explain why this PDS is being rejected…"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection
