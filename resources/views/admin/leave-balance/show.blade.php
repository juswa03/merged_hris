@extends('admin.layouts.app')

@section('title', 'Leave Balances - {{ $employee->full_name }}')

@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header
        title="Leave Balance Details"
        description="{{ $employee->full_name }} — {{ $employee->employee_id }}"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.leave-balance.adjust', $employee) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                <i class="fas fa-edit mr-2"></i>Adjust Balance
            </a>
            <x-admin.action-button href="{{ route('admin.leave-balance.index') }}" variant="secondary" icon="fas fa-arrow-left">
                Back
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Employee Card -->
    <div class="bg-white rounded-xl shadow-sm border p-5 mb-6 flex flex-wrap items-center gap-4">
        @if($employee->photo_url)
            <img src="{{ asset('storage/'.$employee->photo_url) }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow" alt="">
        @else
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold border-2 border-white shadow">
                {{ strtoupper(substr($employee->first_name,0,1).substr($employee->last_name,0,1)) }}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-semibold text-gray-900">{{ $employee->full_name }}</h2>
            <div class="flex flex-wrap gap-x-6 gap-y-1 mt-1 text-sm text-gray-500">
                <span><i class="fas fa-id-badge mr-1"></i>#{{ $employee->id }}</span>
                <span><i class="fas fa-building mr-1"></i>{{ $employee->department->name ?? '—' }}</span>
                <span><i class="fas fa-briefcase mr-1"></i>{{ $employee->position->name ?? '—' }}</span>
            </div>
        </div>

        <!-- Year Filter -->
        <form method="GET" class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Year:</label>
            <select name="year" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    @if(session('success'))
        <x-admin.alert type="success" dismissible class="mb-6">{{ session('success') }}</x-admin.alert>
    @endif

    <!-- Balance Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach(\App\Models\LeaveBalance::LEAVE_TYPES as $type => $label)
            @php $balance = $balances->where('leave_type', $type)->first(); @endphp
            <div class="bg-white rounded-xl border shadow-sm p-4">
                <p class="text-xs font-medium text-gray-500 mb-2">{{ $label }}</p>
                @if($balance)
                <div class="space-y-1.5 text-xs text-gray-600">
                    <div class="flex justify-between"><span>Opening</span><span class="font-medium">{{ number_format($balance->opening_balance,3) }}</span></div>
                    <div class="flex justify-between text-green-600"><span>Earned</span><span class="font-medium">+{{ number_format($balance->earned,3) }}</span></div>
                    <div class="flex justify-between text-red-500"><span>Used</span><span class="font-medium">−{{ number_format($balance->used,3) }}</span></div>
                    <div class="flex justify-between pt-1.5 border-t font-semibold text-sm">
                        <span>Balance</span>
                        <span class="{{ $balance->closing_balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ number_format($balance->closing_balance,3) }}
                        </span>
                    </div>
                </div>
                @else
                <p class="text-gray-300 text-sm mt-2">No record</p>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Credit Earnings History -->
    <x-admin.card>
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">
                <i class="fas fa-history mr-2 text-gray-400"></i>Credit Earning History ({{ $year }})
            </h3>
            <!-- Grant Credits Modal Trigger -->
            <button onclick="document.getElementById('grantModal').classList.remove('hidden')"
                    class="px-4 py-1.5 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-1"></i>Grant Credits
            </button>
        </div>

        @if($creditEarnings->isEmpty())
            <div class="p-10 text-center text-gray-400">
                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                No credit earnings recorded for {{ $year }}
            </div>
        @else
        <x-admin.table-wrapper>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credits Earned</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($creditEarnings as $earning)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                {{ \App\Models\LeaveBalance::LEAVE_TYPES[$earning->leave_type] ?? $earning->leave_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($earning->period_from)->format('M d, Y') }}
                            —
                            {{ \Carbon\Carbon::parse($earning->period_to)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">
                            +{{ number_format($earning->credits_earned, 3) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $earning->remarks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-admin.table-wrapper>
        <div class="p-4">{{ $creditEarnings->links() }}</div>
        @endif
    </x-admin.card>
</div>

<!-- Grant Credits Modal -->
<div id="grantModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-base font-semibold">Grant Leave Credits</h3>
            <button onclick="document.getElementById('grantModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.leave-balance.grant-credits', $employee) }}" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type <span class="text-red-500">*</span></label>
                <select name="leave_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    @foreach(\App\Models\LeaveBalance::LEAVE_TYPES as $type => $label)
                        <option value="{{ $type }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Credits to Grant <span class="text-red-500">*</span></label>
                <input type="number" name="credits_earned" step="0.001" min="0.001" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g. 1.250">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period From <span class="text-red-500">*</span></label>
                    <input type="date" name="period_from" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period To <span class="text-red-500">*</span></label>
                    <input type="date" name="period_to" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                          placeholder="Optional notes..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('grantModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-plus mr-1"></i>Grant Credits
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
