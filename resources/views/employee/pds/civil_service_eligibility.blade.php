<tbody>
<tr>
    <td colspan="62" class="pds-section-header">
        <span>IV. CIVIL SERVICE ELIGIBILITY</span>
        <button type="button" id="toggle-eligibility-bg"
            class="btn btn-sm btn-light pds-section-toggle-btn"
            aria-label="Minimize section">
            <span id="eligibility-bg-icon">+</span>
        </button>
    </td>
</tr>
</tbody>

<tbody id="eligibility-bg-body" class="hidden-section">
    <tr>
        <td rowspan="2" colspan="20" class="pds-label text-center align-middle" style="font-size:10px;">
            CAREER SERVICE/ RA 1080 (BOARD/ BAR) <br>
            UNDER SPECIAL LAWS/ CES/ CSEE <br>
            BARANGAY ELIGIBILITY / DRIVER'S LICENSE
        </td>
        <td rowspan="2" colspan="5" class="pds-label text-center align-middle" style="font-size:10px;">
            RATING <br> <small>(If Applicable)</small>
        </td>
        <td rowspan="2" colspan="8" class="pds-label text-center align-middle" style="font-size:10px;">
            DATE OF EXAMINATION / CONFERMENT
        </td>
        <td rowspan="2" colspan="16" class="pds-label text-center align-middle" style="font-size:10px;">
            PLACE OF EXAMINATION / CONFERMENT
        </td>
        <td colspan="13" class="pds-label text-center align-middle" style="font-size:10px;">
            LICENSE (if applicable)
        </td>
    </tr>
    <tr>
        <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">NUMBER</td>
        <td colspan="7" class="pds-label text-center align-middle" style="font-size:10px;">Date of Validity</td>
    </tr>

    {{-- Dynamic Rows --}}
    @foreach($eligibilities ?? [] as $eligibility)
    <tr>
        <input type="hidden" name="eligibility_id[]" value="{{ is_object($eligibility) ? $eligibility->id : '' }}">
        <td colspan="20">
            <input type="text" name="eligibility_type[]" class="form-control pds-input-borderless"
                value="{{ old('eligibility_type.' . ($eligibility->id ?? ''), $eligibility->eligibility_type ?? '') }}">
        </td>
        <td colspan="5">
            <input type="text" name="rating[]" class="form-control pds-input-borderless"
                value="{{ old('rating.' . ($eligibility->id ?? ''), $eligibility->rating ?? '') }}">
        </td>
        <td colspan="8">
            <input type="date" name="exam_date[]" class="form-control pds-input-borderless"
                value="{{ old('exam_date.' . ($eligibility->id ?? ''), $eligibility->exam_date ?? '') }}">
        </td>
        <td colspan="16">
            <input type="text" name="exam_place[]" class="form-control pds-input-borderless"
                value="{{ old('exam_place.' . ($eligibility->id ?? ''), $eligibility->exam_place ?? '') }}">
        </td>
        <td colspan="6">
            <input type="text" name="license_number[]" class="form-control pds-input-borderless"
                value="{{ old('license_number.' . ($eligibility->id ?? ''), $eligibility->license_number ?? '') }}">
        </td>
        <td colspan="7">
            <input type="date" name="license_validity[]" class="form-control pds-input-borderless"
                value="{{ old('license_validity.' . ($eligibility->id ?? ''), $eligibility->license_validity ?? '') }}">
        </td>
    </tr>
    @endforeach

    {{-- Add/Remove Buttons --}}
    <tr>
        <td colspan="62" class="text-end">
            <button type="button" class="btn btn-sm btn-success" onclick="addEligibilityRow()">+ Add</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeEligibilityRow()">- Remove</button>
        </td>
    </tr>
</tbody>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-eligibility-bg');
    const sectionBody = document.getElementById('eligibility-bg-body');
    const icon = document.getElementById('eligibility-bg-icon');

    // Restore open/closed state from localStorage
    let state = localStorage.getItem('eligibility_section_open');
    if(state === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    }

    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if(sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('eligibility_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('eligibility_section_open', 'open');
        }
    });
});

function addEligibilityRow() {
    let table = document.getElementById('eligibility-bg-body');
    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <input type="hidden" name="eligibility_id[]" value="">
        <td colspan="20"><input type="text" name="eligibility_type[]" class="form-control pds-input-borderless"></td>
        <td colspan="5"><input type="text" name="rating[]" class="form-control pds-input-borderless"></td>
        <td colspan="8"><input type="date" name="exam_date[]" class="form-control pds-input-borderless"></td>
        <td colspan="16"><input type="text" name="exam_place[]" class="form-control pds-input-borderless"></td>
        <td colspan="6"><input type="text" name="license_number[]" class="form-control pds-input-borderless"></td>
        <td colspan="7"><input type="date" name="license_validity[]" class="form-control pds-input-borderless"></td>
    `;
    table.insertBefore(newRow, table.lastElementChild);
}

function removeEligibilityRow() {
    let table = document.getElementById('eligibility-bg-body');
    let rows = table.querySelectorAll('tr');
    if (rows.length > 3) {
        table.removeChild(rows[rows.length - 2]);
    }
}
</script>
