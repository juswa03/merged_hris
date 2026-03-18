<tbody>
<tr>
    <td colspan="62" class="pds-section-header">
        VII. SPECIAL SKILLS and HOBBIES
        <button type="button" id="toggle-skills"
            class="btn btn-sm btn-light pds-section-toggle-btn"
            aria-label="Minimize section">
            <span id="skills-icon">+</span>
        </button>
    </td>
</tr>
</tbody>

<tbody id="skills-body" class="hidden-section">
<tr>
    <td colspan="62" class="pds-label" style="font-size:10px;">
        (List special skills and hobbies, non-academic distinctions, or memberships in organizations.)
    </td>
</tr>

<tr>
    <td colspan="21" class="pds-label text-center align-middle" style="font-size:10px;">
        SPECIAL SKILLS / HOBBIES
    </td>
    <td colspan="21" class="pds-label text-center align-middle" style="font-size:10px;">
        NON-ACADEMIC DISTINCTIONS / RECOGNITIONS
    </td>
    <td colspan="20" class="pds-label text-center align-middle" style="font-size:10px;">
        MEMBERSHIP IN ORGANIZATION
    </td>
</tr>

@forelse($special_skills_hobbies ?? [] as $index => $skill)
<tr>
    <td colspan="21">
        <input type="hidden" name="special_skills_hobby_id[]"
            value="{{ old('special_skills_hobby_id.' . $index, isset($skill) && is_object($skill) ? $skill->id : '') }}">
        <input type="text" name="special_skills[]" class="form-control pds-input-borderless"
            value="{{ old('special_skills.' . $index, $skill->special_skills ?? '') }}">
    </td>
    <td colspan="21">
        <input type="text" name="non_academic_distinctions[]" class="form-control pds-input-borderless"
            value="{{ old('non_academic_distinctions.' . $index, $skill->non_academic_distinctions ?? '') }}">
    </td>
    <td colspan="20">
        <input type="text" name="membership_in_organization[]" class="form-control pds-input-borderless"
            value="{{ old('membership_in_organization.' . $index, $skill->membership_in_organization ?? '') }}">
    </td>
</tr>
@empty
<tr>
    <td colspan="21">
        <input type="hidden" name="special_skills_hobby_id[]" value="">
        <input type="text" name="special_skills[]" class="form-control pds-input-borderless">
    </td>
    <td colspan="21">
        <input type="text" name="non_academic_distinctions[]" class="form-control pds-input-borderless">
    </td>
    <td colspan="20">
        <input type="text" name="membership_in_organization[]" class="form-control pds-input-borderless">
    </td>
</tr>
@endforelse

<tr>
    <td colspan="62" class="text-end">
        <button type="button" class="btn btn-sm btn-success" onclick="addSkillsRow()">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeSkillsRow()">- Remove</button>
    </td>
</tr>
</tbody>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-skills');
    const sectionBody = document.getElementById('skills-body');
    const icon = document.getElementById('skills-icon');

    // Restore open/closed state from localStorage
    let state = localStorage.getItem('skills_section_open');
    if(state === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    }

    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if(sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('skills_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('skills_section_open', 'open');
        }
    });
});

function addSkillsRow() {
    let table = document.getElementById('skills-body');
    let newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td colspan="21">
            <input type="hidden" name="special_skills_hobby_id[]" value="">
            <input type="text" name="special_skills[]" class="form-control pds-input-borderless">
        </td>
        <td colspan="21">
            <input type="text" name="non_academic_distinctions[]" class="form-control pds-input-borderless">
        </td>
        <td colspan="20">
            <input type="text" name="membership_in_organization[]" class="form-control pds-input-borderless">
        </td>
    `;
    table.insertBefore(newRow, table.lastElementChild);
}

function removeSkillsRow() {
    let table = document.getElementById('skills-body');
    let rows = table.querySelectorAll('tr');
    if (rows.length > 4) { // keep headers + at least one row
        table.removeChild(rows[rows.length - 2]);
    }
}
</script>
