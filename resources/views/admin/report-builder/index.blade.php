@extends('admin.layouts.app')
@section('title', 'Report Builder')
@section('content')
<div class="container mx-auto px-4 py-6">
    <x-admin.page-header title="Report Builder" description="Build quick exports with filters and custom columns" />

    <x-admin.card class="mb-6">
        <form id="reportForm" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @csrf
            <div class="lg:col-span-1 space-y-3">
                <div>
                    <label class="text-xs text-gray-600 font-semibold">Report Type</label>
                    <select name="report_type" id="report_type" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                        @foreach($reportTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 font-semibold">Department</label>
                    <select name="department_id" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        @foreach($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 font-semibold">Position</label>
                    <select name="position_id" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        @foreach($positions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 font-semibold">Employment Type</label>
                    <select name="employment_type_id" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        @foreach($employmentTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-600 font-semibold">Job Status</label>
                    <select name="job_status_id" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                        <option value="">All</option>
                        @foreach($jobStatuses as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs text-gray-600 font-semibold">Hire From</label>
                        <input type="date" name="hire_date_from" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 font-semibold">Hire To</label>
                        <input type="date" name="hire_date_to" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2" id="leaveYearWrapper">
                    <div>
                        <label class="text-xs text-gray-600 font-semibold">Leave Year</label>
                        <input type="number" name="year" value="{{ date('Y') }}" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 font-semibold">Leave Type</label>
                        <select name="leave_type" class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                            <option value="">All</option>
                            @foreach(\App\Models\LeaveBalance::LEAVE_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" id="previewBtn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-search mr-1"></i>Preview
                    </button>
                    <button type="button" id="exportBtn" class="flex-1 border border-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm hover:bg-blue-50">
                        <i class="fas fa-file-excel mr-1"></i>Export
                    </button>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Columns</h3>
                    <div id="columnList" class="flex flex-wrap gap-2"></div>
                </div>
                <div class="bg-white border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 border-b flex items-center justify-between">
                        <div class="text-sm font-semibold text-gray-700">Preview</div>
                        <div class="text-xs text-gray-400" id="totalRows">0 rows</div>
                    </div>
                    <div class="overflow-x-auto" id="previewArea">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50" id="previewHead"></thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="previewBody"></tbody>
                        </table>
                        <div class="p-6 text-center text-gray-400" id="emptyState">
                            <i class="fas fa-eye text-3xl mb-2 block"></i>
                            Click Preview to see results (max 50 rows shown here).
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </x-admin.card>
</div>

<script>
const allColumns = @json($allColumns);
const form = document.getElementById('reportForm');
const columnList = document.getElementById('columnList');
const previewHead = document.getElementById('previewHead');
const previewBody = document.getElementById('previewBody');
const emptyState = document.getElementById('emptyState');
const totalRows = document.getElementById('totalRows');
const leaveYearWrapper = document.getElementById('leaveYearWrapper');

function renderColumns() {
    const type = document.getElementById('report_type').value;
    columnList.innerHTML = '';
    Object.entries(allColumns[type]).forEach(([key, label]) => {
        const id = 'col_' + key;
        const wrapper = document.createElement('label');
        wrapper.className = 'flex items-center gap-2 px-3 py-2 border rounded-lg text-sm cursor-pointer hover:border-blue-400';
        wrapper.innerHTML = `<input type="checkbox" name="columns[]" value="${key}" id="${id}" class="text-blue-600" checked><span>${label}</span>`;
        columnList.appendChild(wrapper);
    });
    leaveYearWrapper.classList.toggle('hidden', type !== 'leave');
}

async function fetchPreview(exportMode = false) {
    const formData = new FormData(form);
    const columns = [...columnList.querySelectorAll('input[name="columns[]"]:checked')].map(el => el.value);
    if (columns.length === 0) {
        alert('Select at least one column.');
        return;
    }
    formData.delete('columns[]');
    columns.forEach(c => formData.append('columns[]', c));

    if (exportMode) {
        const params = new URLSearchParams(formData);
        window.location = '{{ route('admin.report-builder.export') }}' + '?' + params.toString();
        return;
    }

    const res = await fetch('{{ route('admin.report-builder.generate') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    });
    const data = await res.json();

    // Render table
    previewHead.innerHTML = '';
    previewBody.innerHTML = '';
    const cols = Object.values(data.columns || {});
    if (cols.length === 0 || (data.rows || []).length === 0) {
        emptyState.classList.remove('hidden');
        totalRows.textContent = '0 rows';
        return;
    }
    emptyState.classList.add('hidden');

    const headRow = document.createElement('tr');
    cols.forEach(label => {
        const th = document.createElement('th');
        th.className = 'px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase';
        th.textContent = label;
        headRow.appendChild(th);
    });
    previewHead.appendChild(headRow);

    data.rows.forEach(row => {
        const tr = document.createElement('tr');
        Object.keys(data.columns).forEach(key => {
            const td = document.createElement('td');
            td.className = 'px-4 py-2 text-sm text-gray-700 whitespace-nowrap';
            td.textContent = row[key] ?? '—';
            tr.appendChild(td);
        });
        previewBody.appendChild(tr);
    });
    totalRows.textContent = `${data.total} rows total`;
}

document.getElementById('previewBtn').addEventListener('click', () => fetchPreview(false));
document.getElementById('exportBtn').addEventListener('click', () => fetchPreview(true));
document.getElementById('report_type').addEventListener('change', renderColumns);

renderColumns();
</script>
@endsection
