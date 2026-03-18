<div id="education-bg-sections"></div>

<tbody>
    <tr>
        <td colspan="62" class="pds-section-header">
            <span>III. EDUCATIONAL BACKGROUND</span>
            <button type="button" id="toggle-education-bg"
                class="btn btn-sm btn-light pds-section-toggle-btn"
                aria-label="Minimize section">
                <span id="education-bg-icon">+</span>
            </button>
        </td>
    </tr>
</tbody>

<tbody id="education-bg-body" class="hidden-section">
    <tr>
        <td rowspan="2" colspan="6" class="pds-label text-center align-middle">LEVEL</td>
        <td rowspan="2" colspan="15" class="pds-label text-center align-middle">NAME OF SCHOOL <br> <small>(Write in full)</small></td>
        <td rowspan="2" colspan="15" class="pds-label text-center align-middle">BASIC EDUCATION/ <br> DEGREE/COURSE <br> <small>(Write in full)</small></td>
        <td colspan="10" class="pds-label text-center">PERIOD OF ATTENDANCE</td>
        <td rowspan="2" colspan="5" class="pds-label text-center align-middle">HIGHEST LEVEL/ <br> UNITS EARNED <br> <small>(if not graduated)</small></td>
        <td rowspan="2" colspan="6" class="pds-label text-center align-middle">YEAR GRADUATED</td>
        <td rowspan="2" colspan="5" class="pds-label " style="font-size: 8px">SCHOLARSHIP <br> ACADEMIC HONORS <br> RECEIVED</td>
    </tr>
    <tr>
        <td colspan="5" class="pds-label text-center">From</td>
        <td colspan="5" class="pds-label text-center">To</td>
    </tr>

    {{-- Elementary --}}
    <tr>
        <td colspan="6" class="pds-label" style="font-size: 10px">ELEMENTARY</td>
        <td colspan="15">
            <input type="text" 
                name="elementary_school" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_school', optional($educationalBackgrounds->where('level', 'Elementary')->first())->school_name) }}">
        </td>
        <td colspan="15">
            <input type="text" 
                name="elementary_degree" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_degree', optional($educationalBackgrounds->where('level', 'Elementary')->first())->degree_course) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="elementary_period_from" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_period_from', optional($educationalBackgrounds->where('level', 'Elementary')->first())->period_from) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="elementary_period_to" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_period_to', optional($educationalBackgrounds->where('level', 'Elementary')->first())->period_to) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="elementary_highest_level" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_highest_level', optional($educationalBackgrounds->where('level', 'Elementary')->first())->highest_level_unit_earned) }}">
        </td>
        <td colspan="6">
            <input type="number" 
                name="elementary_year_graduated" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_year_graduated', optional($educationalBackgrounds->where('level', 'Elementary')->first())->year_graduated) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="elementary_honors" 
                class="form-control pds-input-borderless"
                value="{{ old('elementary_honors', optional($educationalBackgrounds->where('level', 'Elementary')->first())->scholarship_honors) }}">
        </td>
    </tr>
    <input type="hidden" name="elementary_level" value="Elementary">

    {{-- Secondary --}}
    <tr>
        <td colspan="6" class="pds-label" style="font-size: 10px">SECONDARY</td>
        <td colspan="15">
            <input type="text" 
                name="secondary_school" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_school', optional($educationalBackgrounds->where('level', 'Secondary')->first())->school_name) }}">
        </td>
        <td colspan="15">
            <input type="text" 
                name="secondary_degree" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_degree', optional($educationalBackgrounds->where('level', 'Secondary')->first())->degree_course) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="secondary_period_from" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_period_from', optional($educationalBackgrounds->where('level', 'Secondary')->first())->period_from) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="secondary_period_to" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_period_to', optional($educationalBackgrounds->where('level', 'Secondary')->first())->period_to) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="secondary_highest_level" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_highest_level', optional($educationalBackgrounds->where('level', 'Secondary')->first())->highest_level_unit_earned) }}">
        </td>
        <td colspan="6">
            <input type="number" 
                name="secondary_year_graduated" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_year_graduated', optional($educationalBackgrounds->where('level', 'Secondary')->first())->year_graduated) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="secondary_honors" 
                class="form-control pds-input-borderless"
                value="{{ old('secondary_honors', optional($educationalBackgrounds->where('level', 'Secondary')->first())->scholarship_honors) }}">
        </td>
    </tr>
    <input type="hidden" name="secondary_level" value="Secondary">

    {{-- Vocational / Trade Course --}}
    <tr>
        <td colspan="6" class="pds-label" style="font-size: 10px">VOCATIONAL / TRADE COURSE</td>
        <td colspan="15">
            <input type="text" 
                name="vocational_school" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_school', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->school_name) }}">
        </td>
        <td colspan="15">
            <input type="text" 
                name="vocational_degree" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_degree', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->degree_course) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="vocational_period_from" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_period_from', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->period_from) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="vocational_period_to" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_period_to', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->period_to) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="vocational_highest_level" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_highest_level', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->highest_level_unit_earned) }}">
        </td>
        <td colspan="6">
            <input type="number" 
                name="vocational_year_graduated" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_year_graduated', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->year_graduated) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="vocational_honors" 
                class="form-control pds-input-borderless"
                value="{{ old('vocational_honors', optional($educationalBackgrounds->where('level', 'VocationalCourse')->first())->scholarship_honors) }}">
        </td>
    </tr>
    <input type="hidden" name="vocational_level" value="VocationalCourse">

    {{-- College --}}
    <tr>
        <td colspan="6" class="pds-label" style="font-size: 10px">COLLEGE</td>
        <td colspan="15">
            <input type="text" 
                name="college_school" 
                class="form-control pds-input-borderless"
                value="{{ old('college_school', optional($educationalBackgrounds->where('level', 'College')->first())->school_name) }}">
        </td>
        <td colspan="15">
            <input type="text" 
                name="college_degree" 
                class="form-control pds-input-borderless"
                value="{{ old('college_degree', optional($educationalBackgrounds->where('level', 'College')->first())->degree_course) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="college_period_from" 
                class="form-control pds-input-borderless"
                value="{{ old('college_period_from', optional($educationalBackgrounds->where('level', 'College')->first())->period_from) }}">
        </td>
        <td colspan="5">
            <input type="number" 
                name="college_period_to" 
                class="form-control pds-input-borderless"
                value="{{ old('college_period_to', optional($educationalBackgrounds->where('level', 'College')->first())->period_to) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="college_highest_level" 
                class="form-control pds-input-borderless"
                value="{{ old('college_highest_level', optional($educationalBackgrounds->where('level', 'College')->first())->highest_level_unit_earned) }}">
        </td>
        <td colspan="6">
            <input type="number" 
                name="college_year_graduated" 
                class="form-control pds-input-borderless"
                value="{{ old('college_year_graduated', optional($educationalBackgrounds->where('level', 'College')->first())->year_graduated) }}">
        </td>
        <td colspan="5">
            <input type="text" 
                name="college_honors" 
                class="form-control pds-input-borderless"
                value="{{ old('college_honors', optional($educationalBackgrounds->where('level', 'College')->first())->scholarship_honors) }}">
        </td>
    </tr>
    <input type="hidden" name="college_level" value="College">

{{-- Graduate Studies --}}
@foreach ($educationalBackgrounds->where('level', 'GraduateStudies') as $index => $graduate)
<tr class="graduate-studies-row">
    <td colspan="6" class="pds-label" style="font-size: 10px">
        GRADUATE STUDIES {{ $loop->iteration }}
    </td>
    <td colspan="15">
        <input type="text" 
            name="graduate[{{ $index }}][school]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.school', $graduate->school_name) }}">
    </td>
    <td colspan="15">
        <input type="text" 
            name="graduate[{{ $index }}][degree]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.degree', $graduate->degree_course) }}">
    </td>
    <td colspan="5">
        <input type="number" 
            name="graduate[{{ $index }}][period_from]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.period_from', $graduate->period_from) }}">
    </td>
    <td colspan="5">
        <input type="number" 
            name="graduate[{{ $index }}][period_to]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.period_to', $graduate->period_to) }}">
    </td>
    <td colspan="5">
        <input type="text" 
            name="graduate[{{ $index }}][highest_level]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.highest_level', $graduate->highest_level_unit_earned) }}">
    </td>
    <td colspan="6">
        <input type="number" 
            name="graduate[{{ $index }}][year_graduated]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.year_graduated', $graduate->year_graduated) }}">
    </td>
    <td colspan="5">
        <input type="text" 
            name="graduate[{{ $index }}][honors]" 
            class="form-control pds-input-borderless"
            value="{{ old('graduate.'.$index.'.honors', $graduate->scholarship_honors) }}">
    </td>
</tr>
<input type="hidden" name="graduate[{{ $index }}][id]" value="{{ $graduate->id }}">
<input type="hidden" name="graduate[{{ $index }}][level]" value="GraduateStudies">
@endforeach

<!-- Add/Remove Graduate Studies Buttons -->
<tr id="graduate-buttons-row">
    <td colspan="62" class="text-center">
        <div class="d-flex justify-content-center gap-2">
            <button type="button" id="add-graduate-btn" class="btn btn-sm btn-success">
                + Add Graduate Studies
            </button>
            <button type="button" id="remove-graduate-btn" class="btn btn-sm btn-danger">
                - Remove
            </button>
        </div>
    </td>
</tr>




</tbody>


<style>
.hidden-section {
    display: none;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ======= EDUCATIONAL BACKGROUND TOGGLE =======
    const toggleBtn = document.getElementById('toggle-education-bg');
    const sectionBody = document.getElementById('education-bg-body');
    const icon = document.getElementById('education-bg-icon');

    // Restore toggle state
    const state = localStorage.getItem('education_section_open');
    if (state === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    } else {
        sectionBody.classList.add('hidden-section');
        icon.textContent = '+';
    }

    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if (sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('education_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('education_section_open', 'open');
        }
    });

    // ======= DYNAMIC GRADUATE STUDIES SECTION =======
    const addGraduateBtn = document.getElementById('add-graduate-btn');
    const removeGraduateBtn = document.getElementById('remove-graduate-btn');
    const graduateContainer = document.getElementById('education-bg-body'); // tbody containing rows
    const buttonsRow = document.getElementById('graduate-buttons-row'); // row with buttons

    // Track number of rows
    let graduateIndex = graduateContainer.querySelectorAll('.graduate-studies-row').length;

    // Function to create a new Graduate Studies row
    function createGraduateRow(index) {
        const newRow = document.createElement('tr');
        newRow.classList.add('graduate-studies-row');

        newRow.innerHTML = `
            <td colspan="6" class="pds-label" style="font-size: 10px">
                GRADUATE STUDIES ${index + 1}
            </td>
            <td colspan="15">
                <input type="text" name="graduate[${index}][school]" class="form-control pds-input-borderless">
            </td>
            <td colspan="15">
                <input type="text" name="graduate[${index}][degree]" class="form-control pds-input-borderless">
            </td>
            <td colspan="5">
                <input type="number" name="graduate[${index}][period_from]" class="form-control pds-input-borderless">
            </td>
            <td colspan="5">
                <input type="number" name="graduate[${index}][period_to]" class="form-control pds-input-borderless">
            </td>
            <td colspan="5">
                <input type="text" name="graduate[${index}][highest_level]" class="form-control pds-input-borderless">
            </td>
            <td colspan="6">
                <input type="number" name="graduate[${index}][year_graduated]" class="form-control pds-input-borderless">
            </td>
            <td colspan="5">
                <input type="text" name="graduate[${index}][honors]" class="form-control pds-input-borderless">
                <input type="hidden" name="graduate[${index}][level]" value="GraduateStudies">
            </td>
        `;

        // Insert the new row before the buttons row
        graduateContainer.insertBefore(newRow, buttonsRow);
    }

    // Add new row
    addGraduateBtn.addEventListener('click', function () {
        createGraduateRow(graduateIndex);
        graduateIndex++;
    });

    // Remove last Graduate Studies row
    removeGraduateBtn.addEventListener('click', function () {
        const rows = graduateContainer.querySelectorAll('.graduate-studies-row');

        if (rows.length > 1) {
            const lastRow = rows[rows.length - 1];
            lastRow.remove();
            graduateIndex--;

            // Re-label rows and re-index input names
            graduateContainer.querySelectorAll('.graduate-studies-row').forEach((row, idx) => {
                // Update label
                const label = row.querySelector('.pds-label');
                if (label) label.textContent = `GRADUATE STUDIES ${idx + 1}`;

                // Update input names
                row.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${idx}]`);
                        input.setAttribute('name', newName);
                    }
                });
            });
        } else {
            alert('At least one Graduate Studies record must remain.');
        }
    });
});
</script>
