<tbody>
<tr>
    <td colspan="62" class="pds-section-header">
        VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S
        <button type="button" id="toggle-membership"
            class="btn btn-sm btn-light pds-section-toggle-btn"
            aria-label="Minimize section">
            <span id="membership-icon">+</span>
        </button>
    </td>
</tr>
</tbody>

<tbody id="membership-body" class="hidden-section">
<tr>
    <td colspan="62" class="pds-label" style="font-size:10px;">
        (Write in full the name & address of organization. Start from your recent involvement.)
    </td>
</tr>
<tr>
    <td rowspan="2" colspan="24" class="pds-label text-center align-middle" style="font-size:10px;">
        NAME & ADDRESS OF ORGANIZATION <br> <small>(Write in full)</small>
    </td>
    <td colspan="12" class="pds-label text-center align-middle" style="font-size:10px;">
        INCLUSIVE DATES <br> <small>(mm/dd/yyyy)</small>
    </td>
    <td rowspan="2" colspan="8" class="pds-label text-center align-middle" style="font-size:10px;">
        NUMBER OF HOURS
    </td>
    <td rowspan="2" colspan="18" class="pds-label text-center align-middle" style="font-size:10px;">
        POSITION / NATURE OF WORK
    </td>
</tr>
<tr>
    <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">FROM</td>
    <td colspan="6" class="pds-label text-center align-middle" style="font-size:10px;">TO</td>
</tr>

@forelse($membership_associations ?? [] as $index => $membership)
<tr>
    <td colspan="24">
        <input type="hidden" name="membership_association_id[]"
            value="{{ old('membership_association_id.' . $index, isset($membership) && is_object($membership) ? $membership->id : '') }}">
        <input type="text" name="organization_name[]" class="form-control pds-input-borderless"
            value="{{ old('organization_name.' . $index, $membership->organization_name ?? '') }}">
    </td>
    <td colspan="6">
        <input type="date" name="period_from[]" class="form-control pds-input-borderless"
            value="{{ old('period_from.' . $index, $membership->period_from ?? '') }}">
    </td>
    <td colspan="6">
        <input type="date" name="period_to[]" class="form-control pds-input-borderless"
            value="{{ old('period_to.' . $index, $membership->period_to ?? '') }}">
    </td>
    <td colspan="8">
        <input type="number" name="number_of_hours[]" class="form-control pds-input-borderless"
            value="{{ old('number_of_hours.' . $index, $membership->number_of_hours ?? '') }}">
    </td>
    <td colspan="18">
        <input type="text" name="position[]" class="form-control pds-input-borderless"
            value="{{ old('position.' . $index, $membership->position ?? '') }}">
    </td>
</tr>
@empty
<tr>
    <td colspan="24"><input type="hidden" name="membership_association_id[]" value="">
        <input type="text" name="organization_name[]" class="form-control pds-input-borderless"></td>
    <td colspan="6"><input type="date" name="period_from[]" class="form-control pds-input-borderless"></td>
    <td colspan="6"><input type="date" name="period_to[]" class="form-control pds-input-borderless"></td>
    <td colspan="8"><input type="number" name="number_of_hours[]" class="form-control pds-input-borderless"></td>
    <td colspan="18"><input type="text" name="position[]" class="form-control pds-input-borderless"></td>
</tr>
@endforelse

<tr>
    <td colspan="62" class="text-end">
        <button type="button" class="btn btn-sm btn-success" onclick="addMembershipRow()">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeMembershipRow()">- Remove</button>
    </td>
</tr>
</tbody>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-membership');
    const sectionBody = document.getElementById('membership-body');
    const icon = document.getElementById('membership-icon');

    // Restore open/closed state from localStorage
    let state = localStorage.getItem('membership_section_open');
    if(state === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    }

    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if(sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('membership_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('membership_section_open', 'open');
        }
    });
});

function addMembershipRow() {
    let table = document.getElementById('membership-body');
    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td colspan="24">
            <input type="hidden" name="membership_association_id[]" value="">
            <input type="text" name="organization_name[]" class="form-control pds-input-borderless">
        </td>
        <td colspan="6"><input type="date" name="period_from[]" class="form-control pds-input-borderless"></td>
        <td colspan="6"><input type="date" name="period_to[]" class="form-control pds-input-borderless"></td>
        <td colspan="8"><input type="number" name="number_of_hours[]" class="form-control pds-input-borderless"></td>
        <td colspan="18"><input type="text" name="position[]" class="form-control pds-input-borderless"></td>
    `;
    table.insertBefore(newRow, table.lastElementChild);
}

function removeMembershipRow() {
    let table = document.getElementById('membership-body');
    let rows = table.querySelectorAll('tr');
    if (rows.length > 4) { // keep headers + at least one row
        table.removeChild(rows[rows.length - 2]);
    }
}
</script>
