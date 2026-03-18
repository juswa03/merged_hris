<tbody>
<tr>
    <td colspan="62" class="pds-section-header">
        <span>V. WORK EXPERIENCE</span>
        <button type="button" id="toggle-work-bg"
            class="btn btn-sm btn-light pds-section-toggle-btn"
            aria-label="Minimize section">
            <span id="work-bg-icon">+</span>
        </button>
    </td>
</tr>
</tbody>

<tbody id="work-bg-body" class="hidden-section">
    <tr>
        <td colspan="62" class="pds-label" style="font-size:10px;">
            (Include private employment. Start from your recent work) <br>
            Description of duties should be indicated in the attached Work Experience sheet.
        </td>
    </tr>

    {{-- Table headers --}}
    <tr>
        <td colspan="12" class="pds-label text-center align-middle" style="font-size:10px;">
            INCLUSIVE DATES <br> <small>(mm/dd/yyyy)</small>
        </td>
        <td rowspan="2" colspan="19" class="pds-label text-center align-middle" style="font-size:10px;">
            POSITION TITLE <br> <small>(Write in full / Do not abbreviate)</small>
        </td>
        <td rowspan="2" colspan="19" class="pds-label text-center align-middle" style="font-size:10px;">
            DEPARTMENT / AGENCY / OFFICE / COMPANY <br> <small>(Write in full / Do not abbreviate)</small>
        </td>
        <!-- <td rowspan="2" colspan="7" class="pds-label text-center align-middle" style="font-size:10px;">
            MONTHLY SALARY
        </td>
        <td rowspan="2" colspan="5" class="pds-label text-center align-middle" style="font-size:10px;">
            SALARY/ JOB/ PAY GRADE & STEP <br> <small>(Format "00-0") / INCREMENT</small>
        </td> -->
        <td rowspan="2" colspan="8" class="pds-label text-center align-middle" style="font-size:10px;">
            STATUS OF APPOINTMENT
        </td>
        <td rowspan="2" colspan="4" class="pds-label text-center align-middle" style="font-size:10px;">
            GOV'T SERVICE <br> <small>(Y/N)</small>
        </td>
    </tr>
    <tr>
        <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">FROM</td>
        <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">TO</td>
    </tr>

    {{-- Dynamic Rows --}}
    @php
        $workOld = old('position_title', []);
    @endphp

    @if(count($workOld) > 0)
        @foreach($workOld as $i => $oldTitle)
        <tr>
            <input type="hidden" name="work_experience_id[]" value="{{ old('work_experience_id.' . $i, '') }}">
            <td colspan="6"><input type="date" name="inclusive_date_from_work[]" class="form-control pds-input-borderless" value="{{ old('inclusive_date_from_work.' . $i) }}"></td>
            <td colspan="6"><input type="date" name="inclusive_date_to_work[]" class="form-control pds-input-borderless" value="{{ old('inclusive_date_to_work.' . $i) }}"></td>
            <td colspan="19"><input type="text" name="position_title[]" class="form-control pds-input-borderless" value="{{ $oldTitle }}"></td>
            <td colspan="19"><input type="text" name="department_agency[]" class="form-control pds-input-borderless" value="{{ old('department_agency.' . $i) }}"></td>
            <!-- <td colspan="7"><input type="number" name="monthly_salary[]" class="form-control pds-input-borderless" value="{{ old('monthly_salary.' . $i) }}"></td>
            <td colspan="5"><input type="text" name="salary_grade_step[]" class="form-control pds-input-borderless" value="{{ old('salary_grade_step.' . $i) }}"></td> -->
            <td colspan="8"><input type="text" name="status_appointment[]" class="form-control pds-input-borderless" value="{{ old('status_appointment.' . $i) }}"></td>
            <td colspan="4">
                <select name="gov_service[]" class="form-control pds-input-borderless">
                    <option value="Y" {{ old('gov_service.' . $i) == 'Y' ? 'selected' : '' }}>Y</option>
                    <option value="N" {{ old('gov_service.' . $i) == 'N' ? 'selected' : '' }}>N</option>
                </select>
            </td>
        </tr>
        @endforeach
    @elseif(isset($work_experiences))
        @foreach($work_experiences as $work)
        <tr>
            <input type="hidden" name="work_experience_id[]" value="{{ $work->id }}">
            <td colspan="6"><input type="date" name="inclusive_date_from_work[]" class="form-control pds-input-borderless" value="{{ $work->inclusive_date_from_work }}"></td>
            <td colspan="6"><input type="date" name="inclusive_date_to_work[]" class="form-control pds-input-borderless" value="{{ $work->inclusive_date_to_work }}"></td>
            <td colspan="19"><input type="text" name="position_title[]" class="form-control pds-input-borderless" value="{{ $work->position_title }}"></td>
            <td colspan="19"><input type="text" name="department_agency[]" class="form-control pds-input-borderless" value="{{ $work->department_agency }}"></td>
            <!-- <td colspan="7"><input type="number" name="monthly_salary[]" class="form-control pds-input-borderless" value="{{ $work->monthly_salary }}"></td>
            <td colspan="5"><input type="text" name="salary_grade_step[]" class="form-control pds-input-borderless" value="{{ $work->salary_grade_step }}"></td> -->
            <td colspan="8"><input type="text" name="status_appointment[]" class="form-control pds-input-borderless" value="{{ $work->status_appointment }}"></td>
            <td colspan="4">
                <select name="gov_service[]" class="form-control pds-input-borderless">
                    <option value="Y" {{ $work->gov_service == 'Y' ? 'selected' : '' }}>Y</option>
                    <option value="N" {{ $work->gov_service == 'N' ? 'selected' : '' }}>N</option>
                </select>
            </td>
        </tr>
        @endforeach
    @endif

    {{-- Add/Remove Buttons --}}
    <tr>
        <td colspan="62" class="text-end">
            <button type="button" class="btn btn-sm btn-success" onclick="addWorkRow()">+ Add</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeWorkRow()">- Remove</button>
        </td>
    </tr>
</tbody>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-work-bg');
    const sectionBody = document.getElementById('work-bg-body');
    const icon = document.getElementById('work-bg-icon');

    // Restore open/closed state from localStorage
    let state = localStorage.getItem('work_section_open');
    if(state === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    }

    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if(sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('work_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('work_section_open', 'open');
        }
    });
});

function addWorkRow() {
    let table = document.getElementById('work-bg-body');
    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <input type="hidden" name="work_experience_id[]" value="">
        <td colspan="6"><input type="date" name="inclusive_date_from_work[]" class="form-control pds-input-borderless"></td>
        <td colspan="6"><input type="date" name="inclusive_date_to_work[]" class="form-control pds-input-borderless"></td>
        <td colspan="19"><input type="text" name="position_title[]" class="form-control pds-input-borderless"></td>
        <td colspan="19"><input type="text" name="department_agency[]" class="form-control pds-input-borderless"></td>
        <td colspan="8"><input type="text" name="status_appointment[]" class="form-control pds-input-borderless"></td>
        <td colspan="4">
            <select name="gov_service[]" class="form-control pds-input-borderless">
                <option value="Y">Y</option>
                <option value="N">N</option>
            </select>
        </td>
    `;
    table.insertBefore(newRow, table.lastElementChild);
}

function removeWorkRow() {
    let table = document.getElementById('work-bg-body');
    let rows = table.querySelectorAll('tr');
    if (rows.length > 4) {
        table.removeChild(rows[rows.length - 2]);
    }
}
</script>
