<tr>
    <td colspan="62" class="pds-section-header">
        <span>II. FAMILY BACKGROUND</span>
        <button type="button" id="toggle-family-bg"
            class="btn btn-sm btn-light pds-section-toggle-btn"
            aria-label="Minimize section">
            <span id="family-bg-icon">+</span>
        </button>
    </td>
</tr>

<tbody id="family-bg-body" class="hidden-section">
    {{-- 22. Spouse Information + Children Header --}}
    <tr>
        <td colspan="7" class="pds-label">22. SPOUSE'S SURNAME</td>
        <td colspan="23">
            <input type="text" name="spouse_last_name"
                class="form-control pds-input-borderless"
                value="{{ old('spouse_last_name', $spouse->last_name ?? '') }}">
        </td>
        {{-- Children Header --}}
        <td class="pds-label" colspan="18" style="font-size:11px">
            23. NAME of CHILDREN<br>(Write full name and list all)
        </td>
        <td class="pds-label" colspan="14" style="font-size:11px">DATE OF BIRTH (mm/dd/yyyy)</td>
    </tr>
    <tr>
        {{-- Spouse First Name --}}
        <td colspan="7" class="pds-label">FIRST NAME</td>
        <td colspan="14">
            <input type="text" name="spouse_first_name"
                class="form-control pds-input-borderless"
                value="{{ old('spouse_first_name', $spouse->first_name ?? '') }}">
        </td>
        <td colspan="5" class="pds-label">Name Extension</td>
        <td colspan="4">
            <input type="text" name="spouse_name_extension"
                class="form-control pds-input-borderless"
                value="{{ old('spouse_name_extension', $spouse->name_extension ?? '') }}">
        </td>
        {{-- Children Section --}}
        <td colspan="32" rowspan="13">
            <table class="table table-borderless mb-0" id="children-table">
                @forelse($children as $index => $child)
                    <tr>
                        <td colspan="9">
                            <input type="hidden" name="children[{{ $index }}][id]"
                                value="{{ $child->id }}">
                            <input type="text" name="children[{{ $index }}][full_name]"
                                class="form-control pds-input-borderless"
                                placeholder="Child's Full Name"
                                value="{{ old("children.$index.full_name", $child->full_name) }}">
                        </td>
                        <td colspan="5">
                            <input type="date" name="children[{{ $index }}][date_of_birth]"
                                class="form-control pds-input-borderless"
                                value="{{ old("children.$index.date_of_birth", $child->date_of_birth) }}">
                        </td>
                        <td colspan="2">
                            <button type="button" class="btn btn-sm btn-danger remove-child">✕</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <input type="text" name="children[0][full_name]"
                                class="form-control pds-input-borderless"
                                placeholder="Child's Full Name">
                        </td>
                        <td colspan="5">
                            <input type="date" name="children[0][date_of_birth]"
                                class="form-control pds-input-borderless">
                        </td>
                        <td colspan="2">
                            <button type="button" class="btn btn-sm btn-danger remove-child">✕</button>
                        </td>
                    </tr>
                @endforelse
            </table>
            {{-- Add Child Button --}}
            <button type="button" class="btn btn-sm btn-success mt-2" id="add-child">+ Add Row</button>
        </td>
    </tr>

    {{-- Spouse Middle Name --}}
    <tr>
        <td colspan="7" class="pds-label">MIDDLE NAME</td>
        <td colspan="23">
            <input type="text" name="spouse_middle_name"
                class="form-control pds-input-borderless"
                value="{{ old('spouse_middle_name', $spouse->middle_name ?? '') }}">
        </td>
    </tr>

    {{-- Spouse Occupation --}}
    <tr>
        <td colspan="7" class="pds-label">OCCUPATION</td>
        <td colspan="23">
            <input type="text" name="spouse_occupation"
                class="form-control pds-input-borderless"
                value="{{ old('spouse_occupation', $spouse->occupation ?? '') }}">
        </td>
    </tr>

    {{-- Employer / Business Name --}}
    <tr>
        <td colspan="7" class="pds-label">EMPLOYER / BUSINESS NAME</td>
        <td colspan="23">
            <input type="text" name="employer_business_name"
                class="form-control pds-input-borderless"
                value="{{ old('employer_business_name', $spouse->employer_business_name ?? '') }}">
        </td>
    </tr>

    {{-- Business Address --}}
    <tr>
        <td colspan="7" class="pds-label">BUSINESS ADDRESS</td>
        <td colspan="23">
            <input type="text" name="business_address"
                class="form-control pds-input-borderless"
                value="{{ old('business_address', $spouse->business_address ?? '') }}">
        </td>
    </tr>
    
    {{-- Spouse Telephone Number --}}
    <tr>
        <td colspan="7" class="pds-label">TELEPHONE NO.</td>
        <td colspan="23">
            <input type="text" name="spouse_telephone_no"
                class="form-control pds-input-borderless"
                value="{{ old('spouse_telephone_no', $spouse->telephone_no ?? '') }}">
        </td>
    </tr>

    {{-- Father's Surname --}}
    <tr>
        <td colspan="7" class="pds-label">FATHER'S SURNAME</td>
        <td colspan="23">
            <input type="text" name="father_last_name"
                class="form-control pds-input-borderless"
                value="{{ old('father_last_name', $parents->father_last_name ?? '') }}">
        </td>
    </tr>

    {{-- Father's First Name / Extension --}}
    <tr>
        <td colspan="7" class="pds-label">FIRST NAME</td>
        <td colspan="14">
            <input type="text" name="father_first_name"
                class="form-control pds-input-borderless"
                value="{{ old('father_first_name', $parents->father_first_name ?? '') }}">
        </td>
        <td colspan="5" class="pds-label">Name Extension</td>
        <td colspan="4">
            <input type="text" name="father_extension_name"
                class="form-control pds-input-borderless"
                value="{{ old('father_extension_name', $parents->father_extension_name ?? '') }}">
        </td>
    </tr>

    {{-- Father's Middle Name --}}
    <tr>
        <td colspan="7" class="pds-label">MIDDLE NAME</td>
        <td colspan="23">
            <input type="text" name="father_middle_name"
                class="form-control pds-input-borderless"
                value="{{ old('father_middle_name', $parents->father_middle_name ?? '') }}">
        </td>
    </tr>

    {{-- Mother's Maiden Name Header --}}
    <tr>
        <td colspan="30" class="pds-label">MOTHER'S MAIDEN NAME</td>
    </tr>

    {{-- Mother's Surname --}}
    <tr>
        <td colspan="7" class="pds-label">SURNAME</td>
        <td colspan="23">
            <input type="text" name="mother_last_name"
                class="form-control pds-input-borderless"
                value="{{ old('mother_last_name', $parents->mother_last_name ?? '') }}">
        </td>
    </tr>

    {{-- Mother's First Name --}}
    <tr>
        <td colspan="7" class="pds-label">FIRST NAME</td>
        <td colspan="23">
            <input type="text" name="mother_first_name"
                class="form-control pds-input-borderless"
                value="{{ old('mother_first_name', $parents->mother_first_name ?? '') }}">
        </td>
    </tr>

    {{-- Mother's Middle Name --}}
    <tr>
        <td colspan="7" class="pds-label">MIDDLE NAME</td>
        <td colspan="23">
            <input type="text" name="mother_middle_name"
                class="form-control pds-input-borderless"
                value="{{ old('mother_middle_name', $parents->mother_middle_name ?? '') }}">
        </td>
    </tr>
</tbody>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-family-bg');
    const sectionBody = document.getElementById('family-bg-body');
    const icon = document.getElementById('family-bg-icon');
    const addChildBtn = document.getElementById('add-child');
    const childrenTable = document.getElementById('children-table');

    // Restore open/closed state
    if (localStorage.getItem('family_section_open') === 'open') {
        sectionBody.classList.remove('hidden-section');
        icon.textContent = '−';
    }

    // Toggle section
    toggleBtn.addEventListener('click', function () {
        sectionBody.classList.toggle('hidden-section');
        if (sectionBody.classList.contains('hidden-section')) {
            icon.textContent = '+';
            localStorage.setItem('family_section_open', 'closed');
        } else {
            icon.textContent = '−';
            localStorage.setItem('family_section_open', 'open');
        }
    });

    // Add Child Row
    addChildBtn.addEventListener('click', function () {
        const index = childrenTable.querySelectorAll('tr').length; // dynamic index
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td colspan="9">
                <input type="text" name="children[${index}][full_name]"
                    class="form-control pds-input-borderless"
                    placeholder="Child's Full Name">
            </td>
            <td colspan="5">
                <input type="date" name="children[${index}][date_of_birth]"
                    class="form-control pds-input-borderless">
            </td>
            <td colspan="2">
                <button type="button" class="btn btn-sm btn-danger remove-child">✕</button>
            </td>
        `;
        childrenTable.appendChild(newRow);
    });

    // Remove Child Row
    childrenTable.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-child')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
