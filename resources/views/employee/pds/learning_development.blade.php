{{-- resources/views/pds/learning_development.blade.php --}}

<tbody>
<tr>
    <td colspan="62" class="pds-section-header">
        <span>VII. LEARNING AND DEVELOPMENT (L&D) INTERVENTIONS/TRAINING PROGRAMS ATTENDED</span>
        <button type="button" id="toggle-ld-bg"
            class="btn btn-sm btn-light pds-section-toggle-btn"
            aria-label="Minimize section">
            <span id="ld-bg-icon">+</span>
        </button>
    </td>
</tr>
</tbody>

{{-- Collapsible body --}}
<tbody id="ld-bg-body" class="hidden-section">
<tr>
    <td colspan="62" class="pds-label" style="font-size:10px;">
        (Write in full the title of learning and development interventions/training programs. Start from your recent training.)
    </td>
</tr>

{{-- Table headers --}}
<tr>
    <td rowspan="2" colspan="22" class="pds-label text-center align-middle" style="font-size:10px;">
        TITLE OF LEARNING AND DEVELOPMENT INTERVENTIONS/TRAINING PROGRAMS <br>
        <small>(Write in full)</small>
    </td>
    <td colspan="12" class="pds-label text-center align-middle" style="font-size:10px;">
        INCLUSIVE DATES OF ATTENDANCE <br> <small>(mm/dd/yyyy)</small>
    </td>
    <td rowspan="2" colspan="8" class="pds-label text-center align-middle" style="font-size:10px;">
        NUMBER OF HOURS
    </td>
    <td rowspan="2" colspan="10" class="pds-label text-center align-middle" style="font-size:10px;">
        TYPE OF LD <br> <small>(Managerial / Supervisory / Technical, etc.)</small>
    </td>
    <td rowspan="2" colspan="10" class="pds-label text-center align-middle" style="font-size:10px;">
        CONDUCTED / SPONSORED BY <br> <small>(Write in full)</small>
    </td>
</tr>
<tr>
    <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">FROM</td>
    <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">TO</td>
</tr>

{{-- Dynamic Rows --}}
@forelse($learning_developments ?? [] as $index => $ld)
<tr>
    <td colspan="22">
        <input type="hidden" name="learning_development_id[]" value="{{ isset($ld) && is_object($ld) ? $ld->id : '' }}"> 
        <input type="text" name="training_title[]" class="form-control pds-input-borderless"
            value="{{ old('training_title.' . $index, $ld->training_title) }}">
    </td>
    <td colspan="6"><input type="date" name="inclusive_date_from[]" class="form-control pds-input-borderless" value="{{ old('inclusive_date_from.' . $index, $ld->inclusive_date_from) }}"></td>
    <td colspan="6"><input type="date" name="inclusive_date_to[]" class="form-control pds-input-borderless" value="{{ old('inclusive_date_to.' . $index, $ld->inclusive_date_to) }}"></td>
    <td colspan="8"><input type="number" name="number_of_hours[]" class="form-control pds-input-borderless" value="{{ old('number_of_hours.' . $index, $ld->number_of_hours) }}"></td>
    <td colspan="10"><input type="text" name="type_of_ld[]" class="form-control pds-input-borderless" value="{{ old('type_of_ld.' . $index, $ld->type_of_ld) }}"></td>
    <td colspan="10"><input type="text" name="conducted_by[]" class="form-control pds-input-borderless" value="{{ old('conducted_by.' . $index, $ld->conducted_by) }}"></td>
</tr>
@empty
<tr>
    <td colspan="22"><input type="hidden" name="learning_development_id[]" value=""><input type="text" name="training_title[]" class="form-control pds-input-borderless"></td>
    <td colspan="6"><input type="date" name="inclusive_date_from[]" class="form-control pds-input-borderless"></td>
    <td colspan="6"><input type="date" name="inclusive_date_to[]" class="form-control pds-input-borderless"></td>
    <td colspan="8"><input type="number" name="number_of_hours[]" class="form-control pds-input-borderless"></td>
    <td colspan="10"><input type="text" name="type_of_ld[]" class="form-control pds-input-borderless"></td>
    <td colspan="10"><input type="text" name="conducted_by[]" class="form-control pds-input-borderless"></td>
</tr>
@endforelse

{{-- Add/Remove Buttons --}}
<tr>
    <td colspan="62" class="text-end">
        <button type="button" class="btn btn-sm btn-success" onclick="addLearningDevelopmentRow()">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeLearningDevelopmentRow()">- Remove</button>
    </td>
</tr>
</tbody>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-ld-bg');
    const sectionBody = document.getElementById('ld-bg-body');
    const icon = document.getElementById('ld-bg-icon');

    // Restore open/closed state
    let state = localStorage.getItem('ld_section_open');
    if(state === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    }

    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if(sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('ld_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('ld_section_open', 'open');
        }
    });
});

function addLearningDevelopmentRow() {
    let table = document.getElementById('ld-bg-body');
    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td colspan="22"><input type="hidden" name="learning_development_id[]" value=""><input type="text" name="training_title[]" class="form-control pds-input-borderless"></td>
        <td colspan="6"><input type="date" name="inclusive_date_from[]" class="form-control pds-input-borderless"></td>
        <td colspan="6"><input type="date" name="inclusive_date_to[]" class="form-control pds-input-borderless"></td>
        <td colspan="8"><input type="number" name="number_of_hours[]" class="form-control pds-input-borderless"></td>
        <td colspan="10"><input type="text" name="type_of_ld[]" class="form-control pds-input-borderless"></td>
        <td colspan="10"><input type="text" name="conducted_by[]" class="form-control pds-input-borderless"></td>
    `;
    table.insertBefore(newRow, table.lastElementChild); // insert before buttons row
}

function removeLearningDevelopmentRow() {
    let table = document.getElementById('ld-bg-body');
    let rows = table.querySelectorAll('tr');
    if (rows.length > 4) { // keep headers + buttons intact
        table.removeChild(rows[rows.length - 2]); 
    }
}
</script>
