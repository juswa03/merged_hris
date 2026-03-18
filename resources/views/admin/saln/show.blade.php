@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="SALN — {{ $saln->user->name ?? 'Employee' }}"
        description="Statement of Assets, Liabilities and Net Worth · {{ $reportingYear }}"
    >
        <x-slot name="actions">
            <x-admin.action-button href="{{ route('admin.saln.index') }}" variant="secondary" icon="fas fa-arrow-left">
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
            <x-admin.card title="SALN Status">
                @php
                    $variant = match($saln->status) {
                        'verified'    => 'success',
                        'submitted'   => 'warning',
                        'flagged'     => 'danger',
                        'in_progress' => 'info',
                        default       => 'default',
                    };
                @endphp
                <div class="mb-4">
                    <x-admin.badge :variant="$variant" class="text-sm px-3 py-1">
                        {{ ucfirst(str_replace('_', ' ', $saln->status)) }}
                    </x-admin.badge>
                </div>
                @if($saln->last_action_at)
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Last action</dt>
                            <dd class="font-medium text-gray-700">{{ $saln->last_action_at->format('M d, Y') }}</dd>
                        </div>
                        @if($saln->lastActionBy)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Action by</dt>
                                <dd class="font-medium text-gray-700">{{ $saln->lastActionBy->name }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif
            </x-admin.card>

            {{-- Workflow Actions --}}
            <x-admin.card title="Actions">
                <div class="space-y-3">
                    @if($saln->status !== 'verified')
                        <form action="{{ route('admin.saln.verify', $saln) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                <i class="fas fa-check-circle mr-2"></i> Mark as Verified
                            </button>
                        </form>
                    @endif

                    @if($saln->status !== 'flagged')
                        <button
                            type="button"
                            onclick="document.getElementById('flagModal').classList.remove('hidden')"
                            class="w-full bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition"
                        >
                            <i class="fas fa-flag mr-2"></i> Flag for Review
                        </button>
                    @endif

                    <form
                        action="{{ route('admin.saln.destroy', $saln) }}"
                        method="POST"
                        onsubmit="return confirm('Delete this SALN record? This cannot be undone.')"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full border border-red-300 text-red-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-50 transition">
                            <i class="fas fa-trash mr-2"></i> Delete Record
                        </button>
                    </form>
                </div>
            </x-admin.card>

            {{-- Net Worth Summary --}}
            @if($totalCosts)
                <x-admin.card title="Financial Summary">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Real Properties</dt>
                            <dd class="font-medium text-gray-700">₱{{ number_format($totalCosts->real_properties_total ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Personal Properties</dt>
                            <dd class="font-medium text-gray-700">₱{{ number_format($totalCosts->personal_property_total ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <dt class="text-gray-500">Total Assets</dt>
                            <dd class="font-medium text-gray-700">₱{{ number_format($totalCosts->total_assets_costs ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Total Liabilities</dt>
                            <dd class="font-medium text-red-600">₱{{ number_format($totalCosts->total_liabilities ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <dt class="font-semibold text-gray-700">Net Worth</dt>
                            <dd class="font-bold text-blue-600">₱{{ number_format($totalCosts->net_worth ?? 0, 2) }}</dd>
                        </div>
                    </dl>
                </x-admin.card>
            @endif
        </div>

        {{-- SALN Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Real Properties --}}
            <x-admin.card title="Real Properties ({{ $realProperties->count() }})">
                @if($realProperties->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-xs text-gray-500 uppercase">
                                    <th class="pb-2 pr-4">Description</th>
                                    <th class="pb-2 pr-4">Kind</th>
                                    <th class="pb-2 pr-4">Location</th>
                                    <th class="pb-2 text-right">Acquisition Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($realProperties as $prop)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $prop->description }}</td>
                                        <td class="py-2 pr-4">{{ $prop->kind }}</td>
                                        <td class="py-2 pr-4 text-gray-500">{{ $prop->location }}</td>
                                        <td class="py-2 text-right">₱{{ number_format($prop->acquisition_cost ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">No real properties declared.</p>
                @endif
            </x-admin.card>

            {{-- Personal Properties --}}
            <x-admin.card title="Personal Properties ({{ $personalProperties->count() }})">
                @if($personalProperties->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-xs text-gray-500 uppercase">
                                    <th class="pb-2 pr-4">Description</th>
                                    <th class="pb-2 pr-4">Year Acquired</th>
                                    <th class="pb-2 text-right">Acquisition Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($personalProperties as $prop)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $prop->description }}</td>
                                        <td class="py-2 pr-4">{{ $prop->year_acquired ?? '—' }}</td>
                                        <td class="py-2 text-right">₱{{ number_format($prop->acquisition_cost ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">No personal properties declared.</p>
                @endif
            </x-admin.card>

            {{-- Liabilities --}}
            <x-admin.card title="Liabilities ({{ $liabilities->count() }})">
                @if($liabilities->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b text-left text-xs text-gray-500 uppercase">
                                    <th class="pb-2 pr-4">Nature/Type</th>
                                    <th class="pb-2 pr-4">Creditor</th>
                                    <th class="pb-2 text-right">Outstanding Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($liabilities as $liability)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $liability->nature_type }}</td>
                                        <td class="py-2 pr-4">{{ $liability->name_of_creditors }}</td>
                                        <td class="py-2 text-right">₱{{ number_format($liability->outstanding_balance ?? 0, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">No liabilities declared.</p>
                @endif
            </x-admin.card>

            {{-- Business Interests --}}
            @if($businessInterests->isNotEmpty())
                <x-admin.card title="Business Interests &amp; Financial Connections">
                    <div class="space-y-2 text-sm">
                        @foreach($businessInterests->where('no_business_interest', false) as $biz)
                            <div class="border rounded p-3">
                                <p class="font-medium">{{ $biz->name_of_business }}</p>
                                <p class="text-gray-500">{{ $biz->business_address }}</p>
                                @if($biz->date_of_acquisition)
                                    <p class="text-xs text-gray-400 mt-1">Acquired: {{ \Carbon\Carbon::parse($biz->date_of_acquisition)->format('M d, Y') }}</p>
                                @endif
                            </div>
                        @endforeach
                        @if($businessInterests->where('no_business_interest', true)->isNotEmpty())
                            <p class="italic text-gray-400">No business interest declared.</p>
                        @endif
                    </div>
                </x-admin.card>
            @endif

            {{-- Relatives in Gov Service --}}
            @if($relatives->isNotEmpty())
                <x-admin.card title="Relatives in Government Service">
                    <div class="space-y-2 text-sm">
                        @foreach($relatives->where('no_relative_in_gov_service', false) as $rel)
                            <div class="border rounded p-3">
                                <p class="font-medium">{{ $rel->name_of_relative }} <span class="text-gray-400 font-normal">({{ $rel->relationship }})</span></p>
                                <p class="text-gray-500">{{ $rel->position_of_relative }} — {{ $rel->name_of_agency }}</p>
                            </div>
                        @endforeach
                        @if($relatives->where('no_relative_in_gov_service', true)->isNotEmpty())
                            <p class="italic text-gray-400">No relatives in government service declared.</p>
                        @endif
                    </div>
                </x-admin.card>
            @endif

        </div>
    </div>
</div>

{{-- Flag Modal --}}
<div id="flagModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Flag SALN for Review</h3>
        <form action="{{ route('admin.saln.flag', $saln) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (optional)</label>
                <textarea name="remarks" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Reason for flagging…"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('flagModal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600">Flag</button>
            </div>
        </form>
    </div>
</div>
@endsection
